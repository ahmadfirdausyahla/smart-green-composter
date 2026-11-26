<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DeviceHealthStatus;
use App\Enums\DeviceStatus;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\City;
use App\Models\Device;
use App\Models\DeviceConfiguration;
use App\Models\DeviceRequest;
use App\Models\InventoryTransaction;
use App\Models\Neighborhood;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // 1. Available Central Stock Devices (unassigned to any city or RT/RW)
        $centralAvailableDevices = Device::whereNull('city_id')
            ->whereNull('neighborhood_id')
            ->orderBy('device_code')
            ->get();
        $centralStock = $centralAvailableDevices->count();

        // 2. Available City Stock Devices (assigned to user's city, not yet assigned to RT/RW)
        $cityAvailableDevices = collect();
        if ($user->isAdminKota() && $user->city_id) {
            $cityAvailableDevices = Device::where('city_id', $user->city_id)
                ->whereNull('neighborhood_id')
                ->orderBy('device_code')
                ->get();
        }
        $cityStock = $cityAvailableDevices->count();

        // 3. Devices list based on jurisdiction
        $devicesQuery = Device::with(['city', 'neighborhood.village'])->latest();
        if ($user->isAdminKota()) {
            $devicesQuery->where('city_id', $user->city_id);
        }
        $devices = $devicesQuery->paginate(15);

        // 4. Neighborhoods in user's city (for distribution dropdown)
        $neighborhoods = collect();
        if ($user->isAdminKota() && $user->city_id) {
            $neighborhoods = Neighborhood::whereHas('village.district', function ($q) use ($user) {
                $q->where('city_id', $user->city_id);
            })->get();
        }

        // 5. Cities list (for Master Admin allocation dropdown)
        $cities = $user->isMasterAdmin() ? City::with('province')->get() : collect();

        // 6. Pending DeviceRequest passed from "Setujui & Distribusi"
        $pendingRequest = null;
        $preselectedDeviceIds = [];
        if ($request->filled('request_id')) {
            $candidate = DeviceRequest::with(['city', 'neighborhood.village', 'requester'])
                ->find($request->query('request_id'));

            if ($candidate && in_array($candidate->status, ['pending', 'approved', 'processing'])) {
                $isAuthorized = $user->isMasterAdmin()
                    || ($user->isAdminKota() && (int) $candidate->city_id === (int) $user->city_id && $candidate->neighborhood_id !== null);

                if ($isAuthorized) {
                    $pendingRequest = $candidate;
                    $pool = $candidate->neighborhood_id === null
                        ? $centralAvailableDevices
                        : $cityAvailableDevices;

                    $preselectedDeviceIds = $pool->shuffle()
                        ->take($candidate->requested_count)
                        ->pluck('id')
                        ->all();
                }
            }
        }

        // 7. Transactions history
        $transactions = InventoryTransaction::with(['device', 'performedBy'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.inventory.index', compact(
            'user',
            'centralAvailableDevices',
            'centralStock',
            'cityAvailableDevices',
            'cityStock',
            'devices',
            'neighborhoods',
            'cities',
            'transactions',
            'pendingRequest',
            'preselectedDeviceIds'
        ));
    }

    /**
     * Master Admin: Tambah 1 alat baru dengan Nama Alat yang unik (1 nama = 1 alat).
     */
    public function addCentralStock(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isMasterAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'device_name' => ['required', 'string', 'max:100', 'unique:devices,device_code'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'device_name.unique' => 'Nama alat sudah digunakan. Setiap alat harus memiliki nama unik yang berbeda.',
        ]);

        DB::transaction(function () use ($validated, $user) {
            $serial = 'SN-' . date('Y') . '-' . strtoupper(Str::random(6));

            $device = Device::create([
                'serial_number' => $serial,
                'device_code' => $validated['device_name'],
                'model_name' => $validated['device_name'],
                'api_key' => Str::random(32),
                'city_id' => null,
                'neighborhood_id' => null,
                'current_status' => DeviceStatus::IDLE,
                'health_status' => DeviceHealthStatus::HEALTHY,
                'is_publicly_visible' => false,
                'notes' => $validated['notes'] ?? null,
            ]);

            DeviceConfiguration::create([
                'device_id' => $device->id,
                'min_temp_limit' => 20.00,
                'max_temp_limit' => 65.00,
                'critical_temp_cutoff' => 70.00,
                'default_timer_minutes' => 240,
                'auto_shutdown_enabled' => true,
                'updated_by' => $user->id,
            ]);

            InventoryTransaction::create([
                'transaction_type' => 'central_in',
                'device_id' => $device->id,
                'from_entity' => 'Pabrik / Vendor Manufaktur',
                'to_entity' => 'Gudang Pusat ZeroWaste Green',
                'performed_by' => $user->id,
                'notes' => "Registrasi alat baru: {$validated['device_name']}. " . ($validated['notes'] ?? ''),
            ]);

            AuditLog::record(
                action: 'CENTRAL_STOCK_ADDED',
                description: "Master Admin {$user->name} menambahkan alat baru {$validated['device_name']} ke stok pusat.",
                entityType: 'Device',
                entityId: $device->id
            );
        });

        return back()->with('success', "Alat \"{$validated['device_name']}\" berhasil ditambahkan ke stok pusat.");
    }

    /**
     * Master Admin: Update Nama Alat & catatan perangkat.
     */
    public function updateDevice(Request $request, Device $device): RedirectResponse
    {
        if (! $request->user()->isMasterAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'device_name' => ['required', 'string', 'max:100', 'unique:devices,device_code,' . $device->id],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'device_name.unique' => 'Nama alat sudah digunakan oleh perangkat lain.',
        ]);

        $oldName = $device->device_code;
        $device->update([
            'device_code' => $validated['device_name'],
            'model_name' => $validated['device_name'],
            'notes' => $validated['notes'] ?? $device->notes,
        ]);

        AuditLog::record(
            action: 'DEVICE_UPDATED',
            description: "Master Admin {$request->user()->name} mengubah nama alat {$oldName} menjadi {$validated['device_name']}.",
            entityType: 'Device',
            entityId: $device->id
        );

        return back()->with('success', "Data alat \"{$validated['device_name']}\" berhasil diperbarui.");
    }

    /**
     * Master Admin: Hapus alat yang belum terpasang di RT/RW.
     */
    public function destroyDevice(Request $request, Device $device): RedirectResponse
    {
        if (! $request->user()->isMasterAdmin()) {
            abort(403);
        }

        if ($device->neighborhood_id !== null) {
            return back()->with('error', 'Alat yang sudah terpasang di RT/RW tidak dapat dihapus langsung.');
        }

        $name = $device->device_code;
        $device->delete();

        AuditLog::record(
            action: 'DEVICE_DELETED',
            description: "Master Admin {$request->user()->name} menghapus alat {$name} dari inventaris."
        );

        return back()->with('success', "Alat \"{$name}\" berhasil dihapus.");
    }

    /**
     * Master Admin: Alokasikan alat secara acak (atau pilihan) dari Gudang Pusat ke Kota.
     */
    public function allocateToCity(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isMasterAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'city_id' => ['required', 'exists:cities,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:500'],
            'device_ids' => ['nullable', 'array'],
            'device_ids.*' => ['integer', 'exists:devices,id'],
            'device_request_id' => ['nullable', 'exists:device_requests,id'],
        ]);

        $city = City::findOrFail($validated['city_id']);

        if (! empty($validated['device_ids'])) {
            $pickedDevices = Device::whereNull('city_id')
                ->whereNull('neighborhood_id')
                ->whereIn('id', $validated['device_ids'])
                ->get();
        } else {
            $qty = (int) ($validated['quantity'] ?? 1);
            $pickedDevices = Device::whereNull('city_id')
                ->whereNull('neighborhood_id')
                ->inRandomOrder()
                ->limit($qty)
                ->get();

            if ($pickedDevices->count() < $qty) {
                return back()->with('error', "Stok alat di Gudang Pusat tidak mencukupi (tersedia {$pickedDevices->count()} unit, diminta {$qty} unit).");
            }
        }

        if ($pickedDevices->isEmpty()) {
            return back()->with('error', 'Tidak ada unit alat tersedia di Gudang Pusat untuk dialokasikan.');
        }

        $deviceNames = $pickedDevices->pluck('device_code')->implode(', ');

        DB::transaction(function () use ($pickedDevices, $city, $user, $deviceNames, $validated) {
            foreach ($pickedDevices as $device) {
                $device->update(['city_id' => $city->id]);

                InventoryTransaction::create([
                    'transaction_type' => 'city_allocation',
                    'device_id' => $device->id,
                    'from_entity' => 'Gudang Pusat',
                    'to_entity' => "Gudang {$city->name}",
                    'performed_by' => $user->id,
                    'notes' => "Alokasi acak unit {$device->device_code} ke {$city->name}.",
                ]);
            }

            if (! empty($validated['device_request_id'])) {
                $devReq = DeviceRequest::find($validated['device_request_id']);
                if ($devReq) {
                    $devReq->update([
                        'status' => 'completed',
                        'reviewed_by' => $user->id,
                        'review_notes' => "Disetujui & dikirim acak ke {$city->name}: {$deviceNames}",
                    ]);
                }
            }

            AuditLog::record(
                action: 'STOCK_ALLOCATED_TO_CITY',
                description: "Master Admin {$user->name} mengalokasikan {$pickedDevices->count()} unit ({$deviceNames}) ke {$city->name}."
            );
        });

        return redirect()->route('admin.inventory.index')
            ->with('success', "Berhasil mengirim {$pickedDevices->count()} unit ({$deviceNames}) ke {$city->name}.");
    }

    /**
     * Admin Kota (atau Master Admin): Distribusikan alat secara acak (atau pilihan) dari stok kota ke RT/RW.
     */
    public function distributeToRtRw(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isAdminKota() && ! $user->isMasterAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'neighborhood_id' => ['required', 'exists:neighborhoods,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:50'],
            'device_ids' => ['nullable', 'array'],
            'device_ids.*' => ['integer', 'exists:devices,id'],
            'device_request_id' => ['nullable', 'exists:device_requests,id'],
        ]);

        $neighborhood = Neighborhood::with('village.district')->findOrFail($validated['neighborhood_id']);
        $targetCityId = $user->isAdminKota() ? $user->city_id : $neighborhood->village->district->city_id;

        if (! empty($validated['device_ids'])) {
            $pickedDevices = Device::where('city_id', $targetCityId)
                ->whereNull('neighborhood_id')
                ->whereIn('id', $validated['device_ids'])
                ->get();
        } else {
            $qty = (int) ($validated['quantity'] ?? 1);
            $pickedDevices = Device::where('city_id', $targetCityId)
                ->whereNull('neighborhood_id')
                ->inRandomOrder()
                ->limit($qty)
                ->get();

            if ($pickedDevices->count() < $qty) {
                return back()->with('error', "Stok gudang kota tidak mencukupi (tersedia {$pickedDevices->count()} unit, diminta {$qty} unit).");
            }
        }

        if ($pickedDevices->isEmpty()) {
            return back()->with('error', 'Stok gudang kota Anda habis. Silakan ajukan penambahan alat ke Master Admin terlebih dahulu.');
        }

        $deviceNames = $pickedDevices->pluck('device_code')->implode(', ');

        DB::transaction(function () use ($pickedDevices, $neighborhood, $user, $deviceNames, $validated) {
            foreach ($pickedDevices as $device) {
                $device->update([
                    'neighborhood_id' => $neighborhood->id,
                    'current_status' => DeviceStatus::IDLE,
                    'health_status' => DeviceHealthStatus::HEALTHY,
                    'installed_at' => now(),
                    'is_publicly_visible' => true,
                ]);

                InventoryTransaction::create([
                    'transaction_type' => 'rtrw_deployment',
                    'device_id' => $device->id,
                    'from_entity' => 'Gudang ' . ($device->city->name ?? 'Kota'),
                    'to_entity' => "RT {$neighborhood->rt_number} / RW {$neighborhood->rw_number} ({$neighborhood->village->name})",
                    'performed_by' => $user->id,
                    'notes' => "Distribusi acak unit {$device->device_code} ke lingkungan warga.",
                ]);

                AuditLog::record(
                    action: 'DEVICE_DISTRIBUTED_TO_RTRW',
                    description: "{$user->name} mendistribusikan unit {$device->device_code} ke RT {$neighborhood->rt_number} / RW {$neighborhood->rw_number}.",
                    entityType: 'Device',
                    entityId: $device->id
                );
            }

            if (! empty($validated['device_request_id'])) {
                $devReq = DeviceRequest::find($validated['device_request_id']);
                if ($devReq) {
                    $devReq->update([
                        'status' => 'completed',
                        'reviewed_by' => $user->id,
                        'review_notes' => "Disetujui & dikirim acak ke RT {$neighborhood->rt_number}/RW {$neighborhood->rw_number}: {$deviceNames}",
                    ]);
                }
            }
        });

        return redirect()->route('admin.inventory.index')
            ->with('success', "Berhasil mengirim {$pickedDevices->count()} unit ({$deviceNames}) ke RT {$neighborhood->rt_number} / RW {$neighborhood->rw_number}.");
    }
}
