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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique();
            $table->string('device_code')->unique();
            $table->string('model_name')->default('Smart Green Composter v1');
            $table->string('api_key', 64)->unique();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('neighborhood_id')->nullable()->constrained('neighborhoods')->nullOnDelete();
            $table->enum('current_status', ['offline', 'idle', 'running', 'warning', 'critical'])->default('offline');
            $table->enum('health_status', ['healthy', 'sensor_fault', 'mechanical_fault'])->default('healthy');
            $table->decimal('current_temperature', 5, 2)->default(0.00);
            $table->decimal('current_weight', 6, 2)->default(0.00);
            $table->timestamp('last_heartbeat_at')->nullable();
            $table->timestamp('installed_at')->nullable();
            $table->boolean('is_publicly_visible')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('device_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->unique()->constrained('devices')->cascadeOnDelete();
            $table->decimal('min_temp_limit', 5, 2)->default(20.00);
            $table->decimal('max_temp_limit', 5, 2)->default(65.00);
            $table->decimal('critical_temp_cutoff', 5, 2)->default(70.00);
            $table->integer('default_timer_minutes')->default(240); // 4 hours default
            $table->boolean('auto_shutdown_enabled')->default(true);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('device_inventories', function (Blueprint $table) {
            $table->id();
            $table->string('model_name');
            $table->integer('central_stock')->default(0);
            $table->foreignId('city_id')->nullable()->constrained('cities')->cascadeOnDelete();
            $table->integer('city_stock')->default(0);
            $table->timestamps();
        });

        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('transaction_type', ['central_in', 'city_allocation', 'rtrw_deployment', 'maintenance_return']);
            $table->foreignId('device_id')->nullable()->constrained('devices')->nullOnDelete();
            $table->string('from_entity');
            $table->string('to_entity');
            $table->foreignId('performed_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
        Schema::dropIfExists('device_inventories');
        Schema::dropIfExists('device_configurations');
        Schema::dropIfExists('devices');
    }
};
