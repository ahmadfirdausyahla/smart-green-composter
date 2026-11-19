<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('operation_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->cascadeOnDelete();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('action_type', ['remote_control', 'run_machine', 'config_update'])->default('run_machine');
            $table->integer('duration_minutes_allowed')->default(60);
            $table->enum('status', ['pending', 'approved', 'rejected', 'expired', 'revoked'])->default('pending');
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('operation_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_uuid', 64)->unique();
            $table->foreignId('device_id')->constrained('devices')->cascadeOnDelete();
            $table->foreignId('operator_id')->constrained('users');
            $table->foreignId('permission_id')->nullable()->constrained('operation_permissions')->nullOnDelete();
            $table->decimal('initial_weight', 6, 2); // Kg
            $table->decimal('final_weight', 6, 2)->nullable(); // Kg
            $table->decimal('weight_reduction_kg', 6, 2)->nullable();
            $table->decimal('reduction_percentage', 5, 2)->nullable();
            $table->integer('target_duration_minutes');
            $table->integer('actual_duration_minutes')->nullable();
            $table->decimal('peak_temperature', 5, 2)->nullable();
            $table->decimal('avg_temperature', 5, 2)->nullable();
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->enum('status', ['running', 'completed_normally', 'stopped_by_user', 'emergency_cutoff', 'timeout'])->default('running');
            $table->text('failure_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('device_commands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->cascadeOnDelete();
            $table->enum('command_type', ['POWER_ON', 'POWER_OFF', 'SET_TIMER', 'SET_TEMP_LIMIT', 'EMERGENCY_STOP', 'PING']);
            $table->json('parameters')->nullable();
            $table->foreignId('issued_by')->constrained('users');
            $table->enum('status', ['queued', 'sent', 'executed', 'failed', 'cancelled'])->default('queued');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->text('response_payload')->nullable();
            $table->timestamps();
        });

        Schema::create('sensor_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->cascadeOnDelete();
            $table->foreignId('operation_session_id')->nullable()->constrained('operation_sessions')->nullOnDelete();
            $table->decimal('temperature', 5, 2);
            $table->decimal('weight', 6, 2);
            $table->boolean('motor_running')->default(false);
            $table->boolean('heater_running')->default(false);
            $table->integer('timer_remaining_sec')->default(0);
            $table->json('sensor_status')->nullable();
            $table->timestamp('recorded_at')->useCurrent();
            $table->timestamps();

            $table->index(['device_id', 'recorded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_readings');
        Schema::dropIfExists('device_commands');
        Schema::dropIfExists('operation_sessions');
        Schema::dropIfExists('operation_permissions');
    }
};
