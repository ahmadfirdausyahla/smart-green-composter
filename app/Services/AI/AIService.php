<?php

namespace App\Services\AI;

use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\AI\Providers\MockAIProvider;
use App\Services\AI\Providers\OpenAIAIProvider;
use Illuminate\Support\Str;

class AIService
{
    protected AIProviderInterface $provider;
    protected KnowledgeBaseService $kbService;

    public function __construct(
        KnowledgeBaseService $kbService,
        ?AIProviderInterface $provider = null
    ) {
        $this->kbService = $kbService;

        if ($provider) {
            $this->provider = $provider;
            return;
        }

        $driver = config('services.ai.driver', env('AI_DRIVER', 'mock'));

        $this->provider = match (strtolower($driver)) {
            'openai' => new OpenAIAIProvider(),
            default => new MockAIProvider(),
        };
    }

    public function getProvider(): AIProviderInterface
    {
        return $this->provider;
    }

    /**
     * Process user chat message through safety guardrails, RAG retrieval, and AI generation.
     */
    public function chat(string $userMessage, ?string $sessionToken = null, ?string $ipAddress = null): array
    {
        $message = trim($userMessage);
        if ($message === '') {
            return [
                'reply' => 'Silakan ketik pertanyaan Anda seputar program Smart Green Composter.',
                'source' => null,
                'session_token' => $sessionToken ?? (string) Str::uuid(),
            ];
        }

        // Safety Guardrails: strictly reject commands to control machines or query sensitive secrets
        $lowered = strtolower($message);
        $forbiddenKeywords = [
            'matikan mesin', 'nyalakan mesin', 'stop mesin', 'start mesin',
            'password', 'api_key', 'token', 'remote', 'bypass', 'hack', 'credential'
        ];

        foreach ($forbiddenKeywords as $bad) {
            if (str_contains($lowered, $bad)) {
                return [
                    'reply' => 'Maaf, saya adalah asisten informasi publik ZeroWaste Green. Saya tidak memiliki akses kendali mesin ataupun data sensitif sistem demi keamanan operasional.',
                    'source' => 'Protokol Keamanan Sistem',
                    'session_token' => $sessionToken ?? (string) Str::uuid(),
                ];
            }
        }

        // 1. Manage Conversation Session
        $token = $sessionToken ?: (string) Str::uuid();
        $conversation = AiConversation::firstOrCreate(
            ['session_token' => $token],
            ['ip_address' => $ipAddress ?? request()->ip()]
        );

        // Store user message
        AiMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'message' => $message,
        ]);

        // 2. RAG Knowledge Base Retrieval
        $relevantChunks = $this->kbService->searchRelevantChunks($message, limit: 3);
        $contextChunks = [];
        $sourceTitle = null;

        foreach ($relevantChunks as $chunk) {
            $contextChunks[] = [
                'text' => $chunk->chunk_text,
                'source' => $chunk->document?->title,
            ];
            if (! $sourceTitle && $chunk->document?->title) {
                $sourceTitle = $chunk->document->title;
            }
        }

        // 3. Load Recent Conversation History
        $history = $conversation->messages()
            ->latest('id')
            ->limit(4)
            ->get()
            ->reverse()
            ->map(fn ($m) => ['role' => $m->role, 'message' => $m->message])
            ->toArray();

        // 4. Generate AI Reply
        $reply = $this->provider->generateReply($message, $contextChunks, $history);

        // Store assistant message
        AiMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'message' => $reply,
            'sources_used' => $contextChunks,
        ]);

        return [
            'reply' => $reply,
            'source' => $sourceTitle ?? ($contextChunks ? 'Basis Pengetahuan ZeroWaste Green' : null),
            'session_token' => $token,
        ];
    }
}
