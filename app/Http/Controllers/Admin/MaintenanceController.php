<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Device;
use App\Models\MaintenanceReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // 1. Devices under jurisdiction
        $devicesQuery = Device::query()->with(['city', 'neighborhood.village']);
        if ($user->isAdminKota()) {
            $devicesQuery->where('city_id', $user->city_id);
        } elseif ($user->isAdminRtRw() || $user->isAdminLapangan()) {
            $devicesQuery->where('neighborhood_id', $user->neighborhood_id);
        }
        $devices = $devicesQuery->get();

        // 2. Reports
        $deviceIds = $devices->pluck('id');
        $reports = MaintenanceReport::with(['device.neighborhood', 'reporter', 'resolver'])
            ->whereIn('device_id', $deviceIds)
            ->latest()
            ->paginate(12);

        return view('admin.maintenance.index', compact('user', 'devices', 'reports'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'device_id' => ['required', 'exists:devices,id'],
            'category' => ['required', 'in:sensor_fault,motor_dead,temperature_abnormal,scale_inaccurate,connectivity_lost,mechanical,other'],
            'priority' => ['required', 'in:low,medium,high,critical'],
            'description' => ['required', 'string', 'min:10', 'max:1500'],
        ]);

        $device = Device::findOrFail($validated['device_id']);

        // Check regional boundary
        if ($user->isAdminLapangan() || $user->isAdminRtRw()) {
            if ((int) $device->neighborhood_id !== (int) $user->neighborhood_id) {
                abort(403);
            }
        }

        $ticket = MaintenanceReport::create([
            'ticket_number' => MaintenanceReport::generateTicketNumber(),
            'device_id' => $device->id,
            'reporter_id' => $user->id,
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'description' => $validated['description'],
            'status' => 'open',
        ]);

        AuditLog::record(
            action: 'MAINTENANCE_TICKET_CREATED',
            description: "Tiket kerusakan {$ticket->ticket_number} untuk {$device->device_code} dibuat oleh {$user->name}.",
            entityType: 'MaintenanceReport',
            entityId: $ticket->id
        );

        return back()->with('success', "Laporan kerusakan {$ticket->ticket_number} berhasil didaftarkan.");
    }

    public function updateStatus(Request $request, MaintenanceReport $report): RedirectResponse
    {
        $user = $request->user();

        // Only RT/RW Admin, Kota Admin, or Master Admin can update ticket status
        if ($user->isAdminLapangan()) {
            abort(403, 'Hanya Admin RT/RW atau Admin Kota yang dapat memperbarui status laporan.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:open,under_review,in_progress,resolved,closed'],
            'resolution_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $report->status = $validated['status'];
        $report->resolution_notes = $validated['resolution_notes'] ?? $report->resolution_notes;

        if (in_array($validated['status'], ['resolved', 'closed'])) {
            $report->resolved_by = $user->id;
            $report->resolved_at = now();
        }

        $report->save();

        AuditLog::record(
            action: 'MAINTENANCE_TICKET_UPDATED',
            description: "Status tiket {$report->ticket_number} diubah menjadi {$validated['status']} oleh {$user->name}.",
            entityType: 'MaintenanceReport',
            entityId: $report->id
        );

        return back()->with('success', "Status laporan {$report->ticket_number} berhasil diperbarui.");
    }
}
