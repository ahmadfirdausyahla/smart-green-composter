<?php

namespace App\Http\Controllers\Public;

use App\Enums\DeviceStatus;
use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\KnowledgeDocument;
use App\Models\OperationSession;
use App\Services\AI\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicController extends Controller
{
    /**
     * 1. Public Home Landing Page
     */
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

    /**
     * 2. About Smart Green Composter Page
     */
    public function about(): View
    {
        return view('public.about');
    }

    /**
     * 3. How It Works Page
     */
    public function howItWorks(): View
    {
        return view('public.how_it_works');
    }

    /**
     * 4. Technology & Features Page
     */
    public function technology(): View
    {
        return view('public.technology');
    }

    /**
     * 5. Device Coverage Map Page (GIS)
     */
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

    /**
     * 6. Environmental Impact & Stats Page
     */
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
     * 7. News & Information Page
     */
    public function news(): View
    {
        return view('public.news');
    }

    /**
     * 8. FAQ Page
     */
    public function faq(): View
    {
        $documents = KnowledgeDocument::where('is_published', true)->with('chunks')->get();
        return view('public.faq', compact('documents'));
    }

    /**
     * 9. Contact & Socialization Request Page
     */
    public function contact(): View
    {
        return view('public.contact');
    }

    /**
     * 10. Standalone Fullscreen AI Chatbot Page
     */
    public function chatbotPage(): View
    {
        return view('public.chatbot');
    }

    /**
     * RAG AI Public Chatbot Endpoint (POST).
     * Powered by AIService with guardrails and RAG context injection.
     */
    public function chatbot(Request $request, AIService $aiService): JsonResponse
    {
        $message = (string) $request->input('message', '');
        $token = $request->input('session_token');

        $result = $aiService->chat($message, $token, $request->ip());

        return response()->json($result);
    }
}
