<?php

namespace App\Services\AI\Contracts;

interface AIProviderInterface
{
    /**
     * Generate an AI assistant reply given user query, retrieved knowledge chunks, and conversation history.
     */
    public function generateReply(string $userMessage, array $contextChunks = [], array $conversationHistory = []): string;

    /**
     * Get the name of this AI provider.
     */
    public function getProviderName(): string;
}
