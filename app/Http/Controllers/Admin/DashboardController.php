<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DeviceHealthStatus;
use App\Enums\DeviceStatus;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Device;
use App\Models\MaintenanceReport;
use App\Models\OperationSession;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // Admin Lapangan redirected to Monitoring directly
        if ($user->isAdminLapangan()) {
            return redirect()->route('admin.monitoring.index');
        }

        // Query devices based on regional boundary
        $deviceQuery = Device::query()->with(['city', 'neighborhood.village']);

        if ($user->isAdminKota()) {
            $deviceQuery->where('city_id', $user->city_id);
        } elseif ($user->isAdminRtRw()) {
            $deviceQuery->where('neighborhood_id', $user->neighborhood_id);
        }

        $devices = $deviceQuery->get();

        // 1. Device Counts
        $totalDevices = $devices->count();
        $activeDevices = $devices->whereIn('current_status', [DeviceStatus::RUNNING, DeviceStatus::IDLE])->count();
        $runningDevices = $devices->where('current_status', DeviceStatus::RUNNING)->count();
        $offlineDevices = $devices->where('current_status', DeviceStatus::OFFLINE)->count();
        $warningDevices = $devices->whereIn('current_status', [DeviceStatus::WARNING, DeviceStatus::CRITICAL])->count();
        $faultyHealthDevices = $devices->where('health_status', '!=', DeviceHealthStatus::HEALTHY)->count();

        // 2. Weight and Mass Reduction Stats
        $deviceIds = $devices->pluck('id');
        $sessionQuery = OperationSession::whereIn('device_id', $deviceIds);

        $totalInitialWeight = (float) $sessionQuery->sum('initial_weight');
        $totalFinalWeight = (float) $sessionQuery->sum('final_weight');
        $totalReductionKg = (float) $sessionQuery->sum('weight_reduction_kg');
        $avgReductionPct = $totalInitialWeight > 0 ? round(($totalReductionKg / $totalInitialWeight) * 100, 1) : 0;

        // 3. Recent Sessions
        $recentSessions = OperationSession::with(['device.neighborhood', 'operator'])
            ->whereIn('device_id', $deviceIds)
            ->latest('started_at')
            ->limit(5)
            ->get();

        // 4. Open Maintenance Reports
        $reportQuery = MaintenanceReport::with(['device', 'reporter'])
            ->whereIn('device_id', $deviceIds)
            ->whereIn('status', ['open', 'under_review', 'in_progress']);

        $openReportsCount = $reportQuery->count();
        $recentReports = $reportQuery->latest()->limit(5)->get();

        // 5. Recent Activity Logs
        $auditLogsQuery = AuditLog::with('user')->latest('created_at')->limit(8);
        if (! $user->isMasterAdmin()) {
            // Non-master admin only sees activities performed by users in their region or on their devices
            $auditLogsQuery->where(function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }
        $recentActivities = $auditLogsQuery->get();

        // 6. Map Pinpoints (Only public safe coordinates)
        $mapPins = $devices->filter(fn ($d) => $d->neighborhood && $d->neighborhood->latitude && $d->neighborhood->longitude)
            ->map(fn ($d) => [
                'id' => $d->id,
                'code' => $d->device_code,
                'model' => $d->model_name,
                'status' => $d->current_status->value,
                'status_label' => $d->current_status->label(),
                'temperature' => $d->current_temperature,
                'weight' => $d->current_weight,
                'lat' => (float) $d->neighborhood->latitude,
                'lng' => (float) $d->neighborhood->longitude,
                'location' => "RT {$d->neighborhood->rt_number} / RW {$d->neighborhood->rw_number}, " . ($d->neighborhood->village->name ?? ''),
            ])->values();

        return view('admin.dashboard.index', compact(
            'user',
            'totalDevices',
            'activeDevices',
            'runningDevices',
            'offlineDevices',
            'warningDevices',
            'faultyHealthDevices',
            'totalInitialWeight',
            'totalFinalWeight',
            'totalReductionKg',
            'avgReductionPct',
            'recentSessions',
            'openReportsCount',
            'recentReports',
            'recentActivities',
            'mapPins'
        ));
    }
}
