<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Device;
use App\Models\Neighborhood;
use App\Models\OperationSession;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Reports and Analytics Dashboard (Section 21).
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Enforce role access: Lapangan is forbidden
        if ($user->isAdminLapangan()) {
            abort(403, 'Akses terbatas untuk peran administratif.');
        }

        $query = OperationSession::with(['device.neighborhood.village.district.city', 'operator']);

        // 1. Regional Boundary Scoping
        if ($user->isAdminKota()) {
            $query->whereHas('device', function ($q) use ($user) {
                $q->where('city_id', $user->city_id);
            });
        } elseif ($user->isAdminRtRw()) {
            $query->whereHas('device', function ($q) use ($user) {
                $q->where('neighborhood_id', $user->neighborhood_id);
            });
        }

        // 2. Date Range Filtering
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($startDate) {
            $query->whereDate('started_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('started_at', '<=', $endDate);
        }

        // 3. Optional Regional Filters (for higher roles)
        $cityId = $request->input('city_id');
        if ($user->isMasterAdmin() && $cityId) {
            $query->whereHas('device', function ($q) use ($cityId) {
                $q->where('city_id', $cityId);
            });
        }

        $neighborhoodId = $request->input('neighborhood_id');
        if (($user->isMasterAdmin() || $user->isAdminKota()) && $neighborhoodId) {
            $query->whereHas('device', function ($q) use ($neighborhoodId) {
                $q->where('neighborhood_id', $neighborhoodId);
            });
        }

        // Aggregate Metrics
        $totalInitialWeight = (float) (clone $query)->sum('initial_weight');
        $totalFinalWeight = (float) (clone $query)->sum('final_weight');
        $totalReductionKg = (float) (clone $query)->sum('weight_reduction_kg');
        $totalSessions = (clone $query)->count();
        $avgDuration = $totalSessions > 0 ? (int) round((clone $query)->avg('actual_duration_minutes')) : 0;

        // Formula: ((Initial - Final) / Initial) * 100
        $reductionPercentage = $totalInitialWeight > 0
            ? round((($totalInitialWeight - $totalFinalWeight) / $totalInitialWeight) * 100, 2)
            : 0.0;

        $sessions = $query->latest('started_at')->paginate(15)->withQueryString();

        // Filter dropdown datasets
        $cities = $user->isMasterAdmin() ? City::orderBy('name')->get() : collect();
        $neighborhoods = collect();
        if ($user->isMasterAdmin()) {
            $neighborhoods = Neighborhood::with('village.district.city')->get();
        } elseif ($user->isAdminKota()) {
            $neighborhoods = Neighborhood::whereHas('village.district', function ($q) use ($user) {
                $q->where('city_id', $user->city_id);
            })->with('village')->get();
        }

        return view('admin.reports.index', compact(
            'user',
            'sessions',
            'totalInitialWeight',
            'totalFinalWeight',
            'totalReductionKg',
            'reductionPercentage',
            'totalSessions',
            'avgDuration',
            'cities',
            'neighborhoods',
            'startDate',
            'endDate',
            'cityId',
            'neighborhoodId'
        ));
    }

    /**
     * Export Filtered Report to CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $user = $request->user();
        if ($user->isAdminLapangan()) {
            abort(403);
        }

        $query = OperationSession::with(['device.neighborhood.village.district.city', 'operator']);

        if ($user->isAdminKota()) {
            $query->whereHas('device', function ($q) use ($user) {
                $q->where('city_id', $user->city_id);
            });
        } elseif ($user->isAdminRtRw()) {
            $query->whereHas('device', function ($q) use ($user) {
                $q->where('neighborhood_id', $user->neighborhood_id);
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('started_at', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('started_at', '<=', $request->input('end_date'));
        }
        if ($user->isMasterAdmin() && $request->filled('city_id')) {
            $query->whereHas('device', fn ($q) => $q->where('city_id', $request->input('city_id')));
        }
        if (($user->isMasterAdmin() || $user->isAdminKota()) && $request->filled('neighborhood_id')) {
            $query->whereHas('device', fn ($q) => $q->where('neighborhood_id', $request->input('neighborhood_id')));
        }

        $records = $query->latest('started_at')->get();
        $filename = 'laporan-pengolahan-kompos-' . date('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($records) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM for Microsoft Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'ID Sesi',
                'Kode Perangkat',
                'Wilayah RT/RW',
                'Kota',
                'Operator',
                'Waktu Mulai',
                'Waktu Selesai',
                'Bobot Awal (Kg)',
                'Bobot Akhir (Kg)',
                'Selisih Reduksi (Kg)',
                'Reduksi Massa Selama Pengolahan (%)',
                'Durasi Aktual (Menit)',
                'Status Operasi',
            ]);

            foreach ($records as $r) {
                $loc = $r->device?->neighborhood
                    ? "RT {$r->device->neighborhood->rt_number} / RW {$r->device->neighborhood->rw_number}, " . ($r->device->neighborhood->village->name ?? '')
                    : '-';
                $cityName = $r->device?->city?->name ?? '-';

                fputcsv($handle, [
                    $r->session_uuid ?? ('SES-' . $r->id),
                    $r->device?->device_code ?? '-',
                    $loc,
                    $cityName,
                    $r->operator?->name ?? 'Petugas Lapangan',
                    $r->started_at ? $r->started_at->format('Y-m-d H:i') : '-',
                    $r->ended_at ? $r->ended_at->format('Y-m-d H:i') : '-',
                    number_format($r->initial_weight, 2, '.', ''),
                    number_format($r->final_weight, 2, '.', ''),
                    number_format($r->weight_reduction_kg, 2, '.', ''),
                    number_format($r->reduction_percentage, 2, '.', '') . '%',
                    $r->actual_duration_minutes ?? $r->target_duration_minutes ?? 240,
                    $r->status,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
