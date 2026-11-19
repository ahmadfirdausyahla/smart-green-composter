<?php

namespace App\Http\Controllers\Public;

use App\Enums\DeviceStatus;
use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\KnowledgeChunk;
use App\Models\OperationSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function home(): View
    {
        $totalDevices = Device::where('is_publicly_visible', true)->count();
        $totalCities = Device::where('is_publicly_visible', true)->distinct('city_id')->count('city_id');
        $totalInitialWeight = (float) OperationSession::sum('initial_weight');
        $totalReductionKg = (float) OperationSession::sum('weight_reduction_kg');
        $avgReductionPct = $totalInitialWeight > 0 ? round(($totalReductionKg / $totalInitialWeight) * 100, 1) : 0;

        $devicesForMap = Device::with(['neighborhood.village.district.city'])
            ->where('is_publicly_visible', true)
            ->whereNotNull('neighborhood_id')
            ->get()
            ->filter(fn ($d) => $d->neighborhood && $d->neighborhood->latitude && $d->neighborhood->longitude)
            ->map(fn ($d) => [
                'code' => $d->device_code,
                'status' => $d->current_status->value,
                'status_label' => $d->current_status->label(),
                'lat' => (float) $d->neighborhood->latitude,
                'lng' => (float) $d->neighborhood->longitude,
                'location' => "RT {$d->neighborhood->rt_number} / RW {$d->neighborhood->rw_number}, " . ($d->neighborhood->village->name ?? '') . ', ' . ($d->neighborhood->village->district->city->name ?? ''),
            ])->values();

        return view('public.home', compact(
            'totalDevices',
            'totalCities',
            'totalInitialWeight',
            'totalReductionKg',
            'avgReductionPct',
            'devicesForMap'
        ));
    }

    public function gis(): View
    {
        $devices = Device::with(['neighborhood.village.district.city.province'])
            ->where('is_publicly_visible', true)
            ->whereNotNull('neighborhood_id')
            ->get()
            ->filter(fn ($d) => $d->neighborhood && $d->neighborhood->latitude && $d->neighborhood->longitude)
            ->map(fn ($d) => [
                'code' => $d->device_code,
                'model' => $d->model_name,
                'status' => $d->current_status->value,
                'status_label' => $d->current_status->label(),
                'lat' => (float) $d->neighborhood->latitude,
                'lng' => (float) $d->neighborhood->longitude,
                'location' => "RT {$d->neighborhood->rt_number} / RW {$d->neighborhood->rw_number}, " . ($d->neighborhood->village->name ?? '') . ', ' . ($d->neighborhood->village->district->city->name ?? ''),
                'province' => $d->neighborhood->village->district->city->province->name ?? '',
                'city' => $d->neighborhood->village->district->city->name ?? '',
            ])->values();

        return view('public.gis', compact('devices'));
    }

    public function stats(): View
    {
        $totalInitialWeight = (float) OperationSession::sum('initial_weight');
        $totalFinalWeight = (float) OperationSession::sum('final_weight');
        $totalReductionKg = (float) OperationSession::sum('weight_reduction_kg');
        $avgReductionPct = $totalInitialWeight > 0 ? round(($totalReductionKg / $totalInitialWeight) * 100, 1) : 0;
        $totalSessions = OperationSession::count();

        $recentSessions = OperationSession::with(['device.neighborhood.village'])
            ->latest('started_at')
            ->limit(10)
            ->get();

        return view('public.stats', compact(
            'totalInitialWeight',
            'totalFinalWeight',
            'totalReductionKg',
            'avgReductionPct',
            'totalSessions',
            'recentSessions'
        ));
    }

    /**
     * RAG AI Public Chatbot endpoint.
     * Uses KnowledgeBase chunks with local text similarity / RAG fallback.
     */
    public function chatbot(Request $request): JsonResponse
    {
        $query = trim((string) $request->input('message', ''));

        if ($query === '') {
            return response()->json(['reply' => 'Silakan ketik pertanyaan Anda seputar program Smart Green Composter.']);
        }

        // Safety Guardrail: Never allow machine control commands or private queries
        $lowered = strtolower($query);
        $forbiddenKeywords = ['matikan mesin', 'nyalakan mesin', 'password', 'api_key', 'token', 'remote', 'bypass', 'hack'];
        foreach ($forbiddenKeywords as $bad) {
            if (str_contains($lowered, $bad)) {
                return response()->json([
                    'reply' => 'Maaf, saya adalah asisten informasi publik ZeroWaste Green. Saya tidak memiliki akses kendali mesin ataupun data sensitif sistem demi keamanan operasional.'
                ]);
            }
        }

        // Simple RAG Matcher based on knowledge chunks
        $keywords = array_filter(explode(' ', preg_replace('/[^\p{L}\p{N}\s]/u', '', $lowered)));
        $chunks = KnowledgeChunk::with('document')->get();

        $bestScore = 0;
        $bestChunk = null;

        foreach ($chunks as $chunk) {
            $chunkText = strtolower($chunk->chunk_text);
            $score = 0;
            foreach ($keywords as $kw) {
                if (strlen($kw) > 2 && str_contains($chunkText, $kw)) {
                    $score += 2;
                }
            }
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestChunk = $chunk;
            }
        }

        if ($bestChunk && $bestScore >= 2) {
            $reply = "Berdasarkan Knowledge Base Smart Green Composter:\n\n" . $bestChunk->chunk_text;
            return response()->json([
                'reply' => $reply,
                'source' => $bestChunk->document->title ?? 'Basis Pengetahuan ZeroWaste Green'
            ]);
        }

        return response()->json([
            'reply' => 'Terima kasih telah bertanya. Informasi spesifik terkait hal tersebut belum tersedia di basis pengetahuan resmi kami. Anda dapat menghubungi pengurus RT/RW setempat atau tim ZeroWaste Green untuk informasi lebih lanjut.',
            'source' => null
        ]);
    }
}
