<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DeviceHealthStatus;
use App\Enums\DeviceStatus;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\City;
use App\Models\Device;
use App\Models\DeviceConfiguration;
use App\Models\DeviceInventory;
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

        // 1. Central Inventory Stocks (Master Admin)
        $centralInventories = DeviceInventory::with('city')->get();

        // 2. City Specific Stocks
        $cityStock = 0;
        if ($user->isAdminKota() && $user->city_id) {
            $cityInv = DeviceInventory::where('city_id', $user->city_id)->first();
            $cityStock = $cityInv ? $cityInv->city_stock : 0;
        }

        // 3. Devices based on jurisdiction
        $devicesQuery = Device::with(['city', 'neighborhood.village']);
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
        $cities = $user->isMasterAdmin() ? City::all() : collect();

        // 6. Transactions history
        $transactionsQuery = InventoryTransaction::with(['device', 'performedBy'])->latest();
        $transactions = $transactionsQuery->limit(10)->get();

        return view('admin.inventory.index', compact(
            'user',
            'centralInventories',
            'cityStock',
            'devices',
            'neighborhoods',
            'cities',
            'transactions'
        ));
    }

    /**
     * Master Admin: Add stock to central inventory.
     */
    public function addCentralStock(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isMasterAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'model_name' => ['required', 'string', 'max:100'],
            'quantity' => ['required', 'integer', 'min:1', 'max:500'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $inv = DeviceInventory::firstOrCreate(
            ['model_name' => $validated['model_name'], 'city_id' => null],
            ['central_stock' => 0]
        );
        $inv->increment('central_stock', $validated['quantity']);

        InventoryTransaction::create([
            'transaction_type' => 'central_in',
            'from_entity' => 'Pabrik / Vendor Manufaktur',
            'to_entity' => 'Gudang Pusat ZeroWaste Green',
            'performed_by' => $user->id,
            'notes' => "Penambahan {$validated['quantity']} unit {$validated['model_name']}. {$validated['notes']}",
        ]);

        AuditLog::record(
            action: 'CENTRAL_STOCK_ADDED',
            description: "Master Admin {$user->name} menambah {$validated['quantity']} unit {$validated['model_name']} ke stok pusat.",
            entityType: 'DeviceInventory',
            entityId: $inv->id
        );

        return back()->with('success', "Berhasil menambahkan {$validated['quantity']} unit ke stok pusat.");
    }

    /**
     * Master Admin: Allocate stock from Central to City.
     */
    public function allocateToCity(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isMasterAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'city_id' => ['required', 'exists:cities,id'],
            'model_name' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $centralInv = DeviceInventory::where('model_name', $validated['model_name'])
            ->whereNull('city_id')
            ->first();

        if (! $centralInv || $centralInv->central_stock < $validated['quantity']) {
            return back()->with('error', 'Stok pusat tidak mencukupi untuk jumlah yang diminta.');
        }

        $city = City::findOrFail($validated['city_id']);

        DB::transaction(function () use ($centralInv, $city, $validated, $user) {
            $centralInv->decrement('central_stock', $validated['quantity']);

            $cityInv = DeviceInventory::firstOrCreate(
                ['model_name' => $validated['model_name'], 'city_id' => $city->id],
                ['city_stock' => 0]
            );
            $cityInv->increment('city_stock', $validated['quantity']);

            InventoryTransaction::create([
                'transaction_type' => 'city_allocation',
                'from_entity' => 'Gudang Pusat',
                'to_entity' => "Gudang {$city->name}",
                'performed_by' => $user->id,
                'notes' => "Alokasi {$validated['quantity']} unit ke {$city->name}.",
            ]);

            AuditLog::record(
                action: 'STOCK_ALLOCATED_TO_CITY',
                description: "Master Admin {$user->name} mengalokasikan {$validated['quantity']} unit {$validated['model_name']} ke {$city->name}."
            );
        });

        return back()->with('success', "Berhasil mengalokasikan {$validated['quantity']} unit ke {$city->name}.");
    }

    /**
     * Admin Kota: Distribute unit to a specific RT/RW.
     */
    public function distributeToRtRw(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isAdminKota()) {
            abort(403);
        }

        $validated = $request->validate([
            'neighborhood_id' => ['required', 'exists:neighborhoods,id'],
            'model_name' => ['required', 'string'],
        ]);

        $cityInv = DeviceInventory::where('city_id', $user->city_id)
            ->where('model_name', $validated['model_name'])
            ->first();

        if (! $cityInv || $cityInv->city_stock < 1) {
            return back()->with('error', 'Stok gudang kota Anda habis. Silakan ajukan penambahan stok ke Master Admin terlebih dahulu.');
        }

        $neighborhood = Neighborhood::findOrFail($validated['neighborhood_id']);

        DB::transaction(function () use ($cityInv, $neighborhood, $validated, $user) {
            $cityInv->decrement('city_stock', 1);

            $cityCode = $user->city ? strtoupper(substr($user->city->name, 0, 3)) : 'DEV';
            $deviceCount = Device::where('city_id', $user->city_id)->count() + 1;
            $code = sprintf('SGC-%s-%03d', $cityCode, $deviceCount);
            $serial = sprintf('SN-%d-%s-%04d', date('Y'), $cityCode, $deviceCount);

            $device = Device::create([
                'serial_number' => $serial,
                'device_code' => $code,
                'model_name' => $validated['model_name'],
                'api_key' => Str::random(32),
                'city_id' => $user->city_id,
                'neighborhood_id' => $neighborhood->id,
                'current_status' => DeviceStatus::IDLE,
                'health_status' => DeviceHealthStatus::HEALTHY,
                'installed_at' => now(),
                'is_publicly_visible' => true,
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
                'transaction_type' => 'rtrw_deployment',
                'device_id' => $device->id,
                'from_entity' => "Gudang {$user->city->name}",
                'to_entity' => "RT {$neighborhood->rt_number} / RW {$neighborhood->rw_number} ({$neighborhood->village->name})",
                'performed_by' => $user->id,
                'notes' => "Pemasangan unit baru {$code} di lingkungan warga.",
            ]);

            AuditLog::record(
                action: 'DEVICE_DISTRIBUTED_TO_RTRW',
                description: "Admin Kota {$user->name} mendistribusikan unit {$code} ke RT {$neighborhood->rt_number} / RW {$neighborhood->rw_number}.",
                entityType: 'Device',
                entityId: $device->id
            );
        });

        return back()->with('success', "Unit komposter berhasil dipasang dan dihubungkan ke lingkungan RT {$neighborhood->rt_number} / RW {$neighborhood->rw_number}.");
    }
}
