<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DeviceStatus;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Device;
use App\Models\DeviceCommand;
use App\Models\OperationPermission;
use App\Models\Province;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // 1. Fetch available devices according to role
        $devicesQuery = Device::query()->with(['city', 'neighborhood.village.district.city.province', 'configuration']);

        if ($user->isAdminKota()) {
            $devicesQuery->where('city_id', $user->city_id);
        } elseif ($user->isAdminRtRw() || $user->isAdminLapangan()) {
            $devicesQuery->where('neighborhood_id', $user->neighborhood_id);
        }

        $devices = $devicesQuery->get();

        // 2. Selected device
        $selectedDeviceId = $request->query('device_id');
        $selectedDevice = null;

        if ($selectedDeviceId) {
            $selectedDevice = $devices->firstWhere('id', (int) $selectedDeviceId);
        } elseif ($devices->count() === 1) {
            // Auto select single device for RT/RW or Lapangan
            $selectedDevice = $devices->first();
        }

        // 3. Check active permission for control
        $hasControlAccess = false;
        $activePermission = null;

        if ($selectedDevice) {
            if ($user->isMasterAdmin() || $user->isAdminRtRw()) {
                // Master admin and RT/RW admin have direct control access
                $hasControlAccess = true;
            } else {
                // Admin Kota and Admin Lapangan require approved unexpired permission
                $activePermission = OperationPermission::where('device_id', $selectedDevice->id)
                    ->where('requester_id', $user->id)
                    ->where('status', 'approved')
                    ->where('expires_at', '>', now())
                    ->latest()
                    ->first();

                $hasControlAccess = (bool) $activePermission;
            }
        }

        // 4. Cascade selector data for Master Admin
        $provinces = $user->isMasterAdmin()
            ? Province::with(['cities.districts.villages.neighborhoods.devices'])->get()
            : collect();

        // 5. Recent commands for selected device
        $recentCommands = $selectedDevice
            ? DeviceCommand::with('issuer')->where('device_id', $selectedDevice->id)->latest()->limit(5)->get()
            : collect();

        // 6. Recent sessions for selected device
        $recentSessions = $selectedDevice
            ? $selectedDevice->operationSessions()->with('operator')->latest('started_at')->limit(4)->get()
            : collect();

        return view('admin.monitoring.index', compact(
            'user',
            'devices',
            'selectedDevice',
            'hasControlAccess',
            'activePermission',
            'provinces',
            'recentCommands',
            'recentSessions'
        ));
    }

    /**
     * Request control permission (for Admin Kota & Admin Lapangan).
     */
    public function requestPermission(Request $request, Device $device): RedirectResponse
    {
        $user = $request->user();

        // Validate regional bounds
        if ($user->isAdminKota() && (int) $device->city_id !== (int) $user->city_id) {
            abort(403);
        }
        if ($user->isAdminLapangan() && (int) $device->neighborhood_id !== (int) $user->neighborhood_id) {
            abort(403);
        }

        $validated = $request->validate([
            'action_type' => ['required', 'in:remote_control,run_machine,config_update'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:180'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $permission = OperationPermission::create([
            'device_id' => $device->id,
            'requester_id' => $user->id,
            'action_type' => $validated['action_type'],
            'duration_minutes_allowed' => $validated['duration_minutes'],
            'status' => 'pending',
            'requested_at' => now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        AuditLog::record(
            action: 'OPERATION_PERMISSION_REQUESTED',
            description: "Pengguna {$user->name} mengajukan izin kendali {$validated['action_type']} untuk perangkat {$device->device_code}.",
            entityType: 'OperationPermission',
            entityId: $permission->id
        );

        return back()->with('success', 'Permintaan izin kendali telah dikirimkan ke Admin RT/RW pemilik perangkat.');
    }

    /**
     * Issue command to ESP32 (Queued with strict verification).
     */
    public function sendCommand(Request $request, Device $device): RedirectResponse
    {
        $user = $request->user();

        // Check permission
        $canControl = false;
        if ($user->isMasterAdmin() || $user->isAdminRtRw()) {
            $canControl = true;
        } else {
            $hasActivePerm = OperationPermission::where('device_id', $device->id)
                ->where('requester_id', $user->id)
                ->where('status', 'approved')
                ->where('expires_at', '>', now())
                ->exists();
            $canControl = $hasActivePerm;
        }

        if (! $canControl) {
            return back()->with('error', 'Akses ditolak: Anda belum memiliki izin kendali aktif dari Admin RT/RW.');
        }

        $validated = $request->validate([
            'command_type' => ['required', 'in:POWER_ON,POWER_OFF,SET_TIMER,SET_TEMP_LIMIT,EMERGENCY_STOP'],
            'timer_minutes' => ['nullable', 'integer', 'min:30', 'max:480'],
            'max_temp' => ['nullable', 'numeric', 'min:40', 'max:70'],
        ]);

        $params = [];
        if ($validated['command_type'] === 'POWER_ON') {
            $params['timer_minutes'] = $validated['timer_minutes'] ?? 240;
            $params['initial_weight'] = $device->current_weight;
        } elseif ($validated['command_type'] === 'SET_TIMER') {
            $params['timer_minutes'] = $validated['timer_minutes'] ?? 240;
        } elseif ($validated['command_type'] === 'SET_TEMP_LIMIT') {
            $params['max_temp'] = $validated['max_temp'] ?? 65.0;
        }

        $cmd = DeviceCommand::create([
            'device_id' => $device->id,
            'command_type' => $validated['command_type'],
            'parameters' => $params,
            'issued_by' => $user->id,
            'status' => 'queued',
        ]);

        AuditLog::record(
            action: 'DEVICE_COMMAND_ISSUED',
            description: "Perintah {$validated['command_type']} dikirim ke perangkat {$device->device_code} oleh {$user->name}.",
            entityType: 'DeviceCommand',
            entityId: $cmd->id
        );

        return back()->with('success', "Instruksi {$validated['command_type']} berhasil dimasukkan ke antrean perangkat {$device->device_code}. Menunggu konfirmasi ESP32.");
    }
}
