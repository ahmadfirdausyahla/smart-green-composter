<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\DeviceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeviceRequestController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = DeviceRequest::with(['neighborhood.village', 'requester', 'reviewer', 'city'])
            ->latest();

        if ($user->isAdminRtRw()) {
            $query->where('neighborhood_id', $user->neighborhood_id);
        } elseif ($user->isAdminKota()) {
            $query->where('city_id', $user->city_id);
        }

        $requests = $query->paginate(15);

        return view('admin.device_requests.index', compact('requests', 'user'));
    }

    /**
     * Admin RT/RW (ke Admin Kota) atau Admin Kota (ke Master Admin): Ajukan penambahan unit alat.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->isAdminRtRw() && $user->neighborhood_id && $user->city_id) {
            $validated = $request->validate([
                'requested_count' => ['required', 'integer', 'min:1', 'max:20'],
                'reason' => ['required', 'string', 'min:5', 'max:1000'],
            ]);

            $devReq = DeviceRequest::create([
                'city_id' => $user->city_id,
                'neighborhood_id' => $user->neighborhood_id,
                'requester_id' => $user->id,
                'requested_count' => $validated['requested_count'],
                'reason' => $validated['reason'],
                'status' => 'pending',
            ]);

            AuditLog::record(
                action: 'DEVICE_ADDITIONAL_REQUESTED',
                description: "Admin RT/RW {$user->name} mengajukan penambahan {$validated['requested_count']} unit komposter.",
                entityType: 'DeviceRequest',
                entityId: $devReq->id
            );

            return back()->with('success', 'Pengajuan unit komposter berhasil dikirimkan ke Admin Kota.');
        }

        if ($user->isAdminKota() && $user->city_id) {
            $validated = $request->validate([
                'requested_count' => ['required', 'integer', 'min:1', 'max:100'],
                'reason' => ['required', 'string', 'min:5', 'max:1000'],
            ]);

            $devReq = DeviceRequest::create([
                'city_id' => $user->city_id,
                'neighborhood_id' => null,
                'requester_id' => $user->id,
                'requested_count' => $validated['requested_count'],
                'reason' => $validated['reason'],
                'status' => 'pending',
            ]);

            AuditLog::record(
                action: 'DEVICE_CITY_STOCK_REQUESTED',
                description: "Admin Kota {$user->name} mengajukan {$validated['requested_count']} unit komposter ke Master Admin.",
                entityType: 'DeviceRequest',
                entityId: $devReq->id
            );

            return back()->with('success', 'Pengajuan stok kota berhasil dikirimkan ke Master Admin.');
        }

        abort(403, 'Anda tidak memiliki wewenang untuk mengajukan penambahan perangkat.');
    }

    /**
     * Master Admin / Admin Kota: Setujui (langsung arahkan ke Form Distribusi) atau Tolak pengajuan.
     */
    public function updateStatus(Request $request, DeviceRequest $deviceRequest): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isMasterAdmin() && (int) $deviceRequest->city_id !== (int) $user->city_id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected,processing,completed'],
            'review_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $deviceRequest->update([
            'status' => $validated['status'],
            'review_notes' => $validated['review_notes'] ?? $deviceRequest->review_notes,
            'reviewed_by' => $user->id,
        ]);

        AuditLog::record(
            action: 'DEVICE_REQUEST_UPDATED',
            description: "Pengajuan unit #{$deviceRequest->id} diubah statusnya menjadi [{$validated['status']}] oleh {$user->name}.",
            entityType: 'DeviceRequest',
            entityId: $deviceRequest->id
        );

        // Langsung arahkan ke Form Distribusi ketika disetujui agar tidak lupa mengirim alat
        if ($validated['status'] === 'approved') {
            return redirect()
                ->route('admin.inventory.index', ['request_id' => $deviceRequest->id])
                ->with('success', 'Pengajuan disetujui. Silakan konfirmasi pengiriman acak alat pada form distribusi di bawah.');
        }

        return back()->with('success', "Status permohonan unit diperbarui menjadi [{$validated['status']}].");
    }
}
