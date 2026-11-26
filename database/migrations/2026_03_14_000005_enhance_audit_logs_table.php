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
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->string('actor_role', 50)->nullable()->after('user_id');
            $table->string('category', 60)->default('SYSTEM')->after('action');
            $table->unsignedBigInteger('device_id')->nullable()->after('entity_id');
            $table->unsignedBigInteger('command_id')->nullable()->after('device_id');
            $table->unsignedBigInteger('approval_id')->nullable()->after('command_id');
            $table->string('result', 30)->default('SUCCESS')->after('description');
            $table->json('metadata')->nullable()->after('new_values');
            $table->string('previous_hash', 64)->nullable()->after('result');
            $table->string('current_hash', 64)->nullable()->after('previous_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumn([
                'actor_role',
                'category',
                'device_id',
                'command_id',
                'approval_id',
                'result',
                'metadata',
                'previous_hash',
                'current_hash',
            ]);
        });
    }
};
