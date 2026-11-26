<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\Audit\AuditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuditLogIntegrityAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    private User $masterAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->masterAdmin = User::create([
            'name' => 'Master Admin Test',
            'email' => 'master.audit@zerowaste.id',
            'password' => Hash::make('password'),
            'role' => UserRole::MASTER_ADMIN,
            'status' => 'active',
        ]);
    }

    public function test_audit_log_cannot_be_updated_at_orm_level(): void
    {
        $log = AuditService::record(
            action: 'TEST_ACTION',
            description: 'Log awal',
            userId: $this->masterAdmin->id
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Audit logs are append-only and immutable.');

        $log->update(['description' => 'Mencoba manipulasi catatan log']);
    }

    public function test_audit_log_cannot_be_deleted_at_orm_level(): void
    {
        $log = AuditService::record(
            action: 'TEST_ACTION_DELETE',
            description: 'Log yang akan dicoba hapus',
            userId: $this->masterAdmin->id
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Audit logs are tamper-evident and append-only.');

        $log->delete();
    }

    public function test_audit_hash_chain_is_valid_when_unaltered(): void
    {
        AuditService::record(action: 'ACTION_1', description: 'Log 1', userId: $this->masterAdmin->id);
        AuditService::record(action: 'ACTION_2', description: 'Log 2', userId: $this->masterAdmin->id);
        AuditService::record(action: 'ACTION_3', description: 'Log 3', userId: $this->masterAdmin->id);

        $verification = AuditService::verifyIntegrity();

        $this->assertTrue($verification['valid']);
        $this->assertNull($verification['corrupted_at_id']);
        $this->assertEquals(3, $verification['total_checked']);
    }

    public function test_audit_hash_chain_detects_tampering_at_database_level(): void
    {
        AuditService::record(action: 'ACTION_1', description: 'Log 1', userId: $this->masterAdmin->id);
        $log2 = AuditService::record(action: 'ACTION_2', description: 'Log 2', userId: $this->masterAdmin->id);
        AuditService::record(action: 'ACTION_3', description: 'Log 3', userId: $this->masterAdmin->id);

        // Simulate unauthorized database modification via direct DB query (bypassing Eloquent model hooks)
        DB::table('audit_logs')
            ->where('id', $log2->id)
            ->update(['current_hash' => 'forged_fake_hash_1234567890abcdef']);

        $verification = AuditService::verifyIntegrity();

        $this->assertFalse($verification['valid']);
        $this->assertNotNull($verification['corrupted_at_id']);
    }

    public function test_admin_can_run_audit_integrity_verification_via_route(): void
    {
        AuditService::record(action: 'SYS_BOOT', description: 'Boot log', userId: $this->masterAdmin->id);

        $response = $this->actingAs($this->masterAdmin)
            ->post('/admin/audit-logs/verify');

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }
}
