<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\AIProviderInterface;

class MockAIProvider implements AIProviderInterface
{
    public function getProviderName(): string
    {
        return 'Mock RAG Engine';
    }

    public function generateReply(string $userMessage, array $contextChunks = [], array $conversationHistory = []): string
    {
        if (! empty($contextChunks)) {
            $contextText = implode("\n\n", array_map(fn ($c) => is_array($c) ? ($c['text'] ?? '') : (string) $c, $contextChunks));
            return "Berdasarkan Basis Pengetahuan Resmi Smart Green Composter:\n\n" . $contextText;
        }

        // Generic friendly fallback when no KB chunk matched
        return "Terima kasih atas pertanyaan Anda seputar program Smart Green Composter. Informasi spesifik mengenai hal tersebut belum tercantum dalam basis pengetahuan kami saat ini. Silakan berkonsultasi dengan pengurus RT/RW setempat atau tim ZeroWaste Green untuk informasi lebih lanjut.";
    }
}
