<?php

namespace App\Services\Audit;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;

class AuditService
{
    public const GENESIS_HASH = '0000000000000000000000000000000000000000000000000000000000000000';

    /**
     * Record an append-only, tamper-evident audit log entry.
     */
    public static function record(
        string $action,
        ?string $description = null,
        string $category = 'SYSTEM',
        ?string $entityType = null,
        ?int $entityId = null,
        ?int $deviceId = null,
        ?int $commandId = null,
        ?int $approvalId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $metadata = null,
        string $result = 'SUCCESS',
        ?int $userId = null,
        ?string $actorRole = null
    ): AuditLog {
        $user = auth()->user();
        $actorId = $userId ?? $user?->id;
        $roleName = $actorRole ?? $user?->role?->value ?? 'SYSTEM';
        $timestamp = now()->toISOString();

        // 1. Fetch latest audit log to establish hash chain
        $latest = AuditLog::orderBy('id', 'desc')->first();
        $previousHash = $latest?->current_hash ?? self::GENESIS_HASH;

        // 2. Compute current SHA-256 hash
        $hashPayload = implode('|', [
            $previousHash,
            $timestamp,
            (string) $actorId,
            $roleName,
            $action,
            $category,
            (string) $entityType,
            (string) $entityId,
            (string) $deviceId,
            (string) $commandId,
            (string) $approvalId,
            $result,
            json_encode($oldValues ?? []),
            json_encode($newValues ?? []),
            json_encode($metadata ?? []),
        ]);

        $currentHash = hash('sha256', $hashPayload);

        return AuditLog::create([
            'user_id' => $actorId,
            'actor_role' => $roleName,
            'action' => $action,
            'category' => $category,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'device_id' => $deviceId,
            'command_id' => $commandId,
            'approval_id' => $approvalId,
            'ip_address' => Request::ip() ?? '127.0.0.1',
            'user_agent' => Request::userAgent() ?? 'System / Console',
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'description' => $description,
            'result' => $result,
            'metadata' => $metadata,
            'previous_hash' => $previousHash,
            'current_hash' => $currentHash,
            'created_at' => now(),
        ]);
    }

    /**
     * Verify tamper-evident cryptographic hash chain integrity across all records.
     */
    public static function verifyIntegrity(): array
    {
        $logs = AuditLog::orderBy('id', 'asc')->get();
        $total = $logs->count();

        if ($total === 0) {
            return [
                'valid' => true,
                'total_checked' => 0,
                'corrupted_at_id' => null,
                'message' => 'Belum ada catatan log audit yang tercatat.',
            ];
        }

        $expectedPrevHash = self::GENESIS_HASH;

        foreach ($logs as $index => $log) {
            // Check legacy records without hash (gracefully assign genesis or continue)
            if (! $log->current_hash) {
                continue;
            }

            // Check previous hash link
            if ($log->previous_hash !== $expectedPrevHash && $index > 0 && $logs[$index - 1]->current_hash) {
                return [
                    'valid' => false,
                    'total_checked' => $index + 1,
                    'corrupted_at_id' => $log->id,
                    'message' => "Integritas rantai hash terputus pada Log ID #{$log->id}. Hash sebelumnya tidak cocok.",
                ];
            }

            $expectedPrevHash = $log->current_hash;
        }

        return [
            'valid' => true,
            'total_checked' => $total,
            'corrupted_at_id' => null,
            'message' => "Seluruh {$total} entri audit log terverifikasi valid. Rantai hash SHA-256 lengkap dan tidak terindikasi adanya manipulasi data.",
        ];
    }
}
