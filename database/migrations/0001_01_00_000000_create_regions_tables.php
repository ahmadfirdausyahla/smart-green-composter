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
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')->constrained('provinces')->cascadeOnDelete();
            $table->string('code', 10)->unique();
            $table->string('name');
            $table->string('type', 20)->default('Kota'); // Kota / Kabupaten
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });

        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
            $table->string('code', 15)->nullable();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('villages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained('districts')->cascadeOnDelete();
            $table->string('code', 20)->nullable();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('neighborhoods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained('villages')->cascadeOnDelete();
            $table->string('rt_number', 10);
            $table->string('rw_number', 10);
            $table->string('name')->nullable(); // e.g. "RT 03 / RW 05"
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();

            $table->unique(['village_id', 'rt_number', 'rw_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('neighborhoods');
        Schema::dropIfExists('villages');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('provinces');
    }
};
