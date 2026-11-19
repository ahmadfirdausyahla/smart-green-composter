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
        Schema::create('registration_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->string('phone_number');
            $table->foreignId('province_id')->constrained('provinces');
            $table->foreignId('city_id')->constrained('cities');
            $table->foreignId('district_id')->constrained('districts');
            $table->foreignId('village_id')->constrained('villages');
            $table->string('rt_number', 10);
            $table->string('rw_number', 10);
            $table->text('supporting_info')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('review_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('device_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained('cities');
            $table->foreignId('neighborhood_id')->constrained('neighborhoods');
            $table->foreignId('requester_id')->constrained('users');
            $table->integer('requested_count')->default(1);
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected', 'processing', 'completed'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('review_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('neighborhood_id')->constrained('neighborhoods')->cascadeOnDelete();
            $table->foreignId('operator_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->date('duty_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('maintenance_reports', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 32)->unique();
            $table->foreignId('device_id')->constrained('devices')->cascadeOnDelete();
            $table->foreignId('reporter_id')->constrained('users');
            $table->enum('category', [
                'sensor_fault',
                'motor_dead',
                'temperature_abnormal',
                'scale_inaccurate',
                'connectivity_lost',
                'mechanical',
                'other'
            ]);
            $table->text('description');
            $table->string('photo_path')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['open', 'under_review', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_reports');
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('device_requests');
        Schema::dropIfExists('registration_requests');
    }
};
