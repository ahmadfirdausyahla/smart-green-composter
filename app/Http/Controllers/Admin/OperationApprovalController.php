<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\OperationPermission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OperationApprovalController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = OperationPermission::with(['device.neighborhood', 'requester', 'approver'])->latest('requested_at');

        if ($user->isAdminRtRw()) {
            $query->whereHas('device', function ($q) use ($user) {
                $q->where('neighborhood_id', $user->neighborhood_id);
            });
        } elseif ($user->isAdminKota()) {
            $query->whereHas('device', function ($q) use ($user) {
                $q->where('city_id', $user->city_id);
            });
        }

        $permissions = $query->paginate(15);

        return view('admin.approvals.index', compact('permissions', 'user'));
    }

    public function respond(Request $request, OperationPermission $permission): RedirectResponse
    {
        $user = $request->user();

        // Check authority: RT/RW admin owning the device or Master Admin
        if (! $user->isMasterAdmin() && (int) $permission->device->neighborhood_id !== (int) $user->neighborhood_id) {
            abort(403, 'Anda hanya dapat memberikan persetujuan untuk perangkat milik RT/RW Anda.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'rejection_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $permission->status = $validated['status'];
        $permission->approver_id = $user->id;
        $permission->responded_at = now();

        if ($validated['status'] === 'approved') {
            $permission->expires_at = now()->addMinutes($permission->duration_minutes_allowed);
        } else {
            $permission->rejection_reason = $validated['rejection_reason'] ?? null;
        }

        $permission->save();

        AuditLog::record(
            action: 'OPERATION_PERMISSION_RESPONDED',
            description: "Izin pengoperasian #{$permission->id} direspons [{$validated['status']}] oleh {$user->name}.",
            entityType: 'OperationPermission',
            entityId: $permission->id
        );

        $msg = $validated['status'] === 'approved'
            ? 'Izin pengoperasian telah disetujui. Operator dapat mengendalikan mesin selama durasi izin.'
            : 'Permintaan izin pengoperasian telah ditolak.';

        return back()->with('success', $msg);
    }
}
