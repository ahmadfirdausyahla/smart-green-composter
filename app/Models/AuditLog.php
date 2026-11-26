<?php

namespace App\Models;

use App\Services\Audit\AuditService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false; // Only created_at exists

    protected $fillable = [
        'user_id',
        'actor_role',
        'action',
        'category',
        'entity_type',
        'entity_id',
        'device_id',
        'command_id',
        'approval_id',
        'ip_address',
        'user_agent',
        'old_values',
        'new_values',
        'description',
        'result',
        'metadata',
        'previous_hash',
        'current_hash',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Enforce append-only requirement at ORM level (Section 19)
        static::updating(function () {
            throw new RuntimeException('Audit logs are append-only and immutable. Modifications are strictly forbidden.');
        });

        static::deleting(function () {
            throw new RuntimeException('Audit logs are tamper-evident and append-only. Deletions are strictly forbidden.');
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public static function record(
        string $action,
        ?string $description = null,
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $userId = null,
        string $category = 'SYSTEM',
        ?int $deviceId = null,
        ?int $commandId = null,
        ?int $approvalId = null,
        ?array $metadata = null,
        string $result = 'SUCCESS'
    ): self {
        return AuditService::record(
            action: $action,
            description: $description,
            category: $category,
            entityType: $entityType,
            entityId: $entityId,
            deviceId: $deviceId,
            commandId: $commandId,
            approvalId: $approvalId,
            oldValues: $oldValues,
            newValues: $newValues,
            metadata: $metadata,
            result: $result,
            userId: $userId
        );
    }
}
