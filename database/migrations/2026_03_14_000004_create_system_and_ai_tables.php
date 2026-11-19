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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('entity_type')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });

        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('endpoint');
            $table->text('p256dh_key');
            $table->text('auth_token');
            $table->json('preferences')->nullable();
            $table->timestamps();
        });

        Schema::create('knowledge_documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->default('general');
            $table->string('source_type')->default('manual');
            $table->longText('content');
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('knowledge_chunks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('knowledge_documents')->cascadeOnDelete();
            $table->text('chunk_text');
            $table->integer('chunk_order')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('session_token', 64)->unique();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('ai_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('ai_conversations')->cascadeOnDelete();
            $table->enum('role', ['user', 'assistant']);
            $table->text('message');
            $table->json('sources_used')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_messages');
        Schema::dropIfExists('ai_conversations');
        Schema::dropIfExists('knowledge_chunks');
        Schema::dropIfExists('knowledge_documents');
        Schema::dropIfExists('push_subscriptions');
        Schema::dropIfExists('audit_logs');
    }
};
