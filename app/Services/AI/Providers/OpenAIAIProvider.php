<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\AIProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIAIProvider implements AIProviderInterface
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $model;
    protected int $timeout;

    public function __construct(array $config = [])
    {
        $this->apiKey = $config['api_key'] ?? config('services.ai.api_key', '');
        $this->baseUrl = rtrim($config['base_url'] ?? config('services.ai.base_url', 'https://api.openai.com/v1'), '/');
        $this->model = $config['model'] ?? config('services.ai.model', 'gpt-4o-mini');
        $this->timeout = (int) ($config['timeout'] ?? config('services.ai.timeout', 30));
    }

    public function getProviderName(): string
    {
        return 'OpenAI API (' . $this->model . ')';
    }

    public function generateReply(string $userMessage, array $contextChunks = [], array $conversationHistory = []): string
    {
        if (empty($this->apiKey)) {
            // Graceful fallback when API key is missing
            if (! empty($contextChunks)) {
                $contextText = implode("\n\n", array_map(fn ($c) => is_array($c) ? ($c['text'] ?? '') : (string) $c, $contextChunks));
                return "Berdasarkan Basis Pengetahuan Resmi Smart Green Composter:\n\n" . $contextText;
            }

            return "Layanan AI eksternal sedang dalam konfigurasi. Silakan merujuk pada basis pengetahuan publik atau hubungi tim pengurus ZeroWaste Green.";
        }

        $systemPrompt = "Anda adalah Asisten Virtual Publik resmi untuk program Smart Green Composter (ZeroWaste Green).\n"
            . "Tugas Anda memberikan edukasi, panduan pemilahan sampah organik, dan informasi program kepada warga masyarakat secara ramah, profesional, dan akurat.\n"
            . "BATASAN KETAT SISTEM:\n"
            . "1. Dilarang keras mencoba atau mengklaim dapat mengendalikan mesin (START, STOP, timer, suhu).\n"
            . "2. Dilarang memberikan atau membocorkan data pribadi warga, operator, password, API key, atau token sistem.\n"
            . "3. Utamakan informasi dari KNOWLEDGE BASE berikut jika relevan:\n";

        if (! empty($contextChunks)) {
            foreach ($contextChunks as $i => $chunk) {
                $text = is_array($chunk) ? ($chunk['text'] ?? '') : (string) $chunk;
                $systemPrompt .= "\n[Dokumen " . ($i + 1) . "]: " . $text;
            }
        } else {
            $systemPrompt .= "\n(Tidak ada potongan dokumen spesifik. Jawablah sesuai prinsip umum pengomposan ramah lingkungan atau sampaikan jika informasi spesifik belum tersedia.)";
        }

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        // Append recent history if any
        foreach (array_slice($conversationHistory, -4) as $h) {
            $messages[] = ['role' => $h['role'] ?? 'user', 'content' => $h['message'] ?? ''];
        }

        $messages[] = ['role' => 'user', 'content' => $userMessage];

        try {
            $response = Http::timeout($this->timeout)
                ->withToken($this->apiKey)
                ->post("{$this->baseUrl}/chat/completions", [
                    'model' => $this->model,
                    'messages' => $messages,
                    'temperature' => 0.4,
                    'max_tokens' => 500,
                ]);

            if ($response->successful()) {
                $reply = $response->json('choices.0.message.content');
                if (! empty($reply)) {
                    return trim($reply);
                }
            }

            Log::warning("OpenAI API response failed with status " . $response->status());
        } catch (\Throwable $e) {
            Log::error("OpenAIAIProvider error: " . $e->getMessage());
        }

        // Fallback on error
        return "Mohon maaf, layanan AI eksternal saat ini sedang mengalami kendala jaringan. Informasi dapat dilihat langsung pada panduan FAQ dan menu publik kami.";
    }
}
