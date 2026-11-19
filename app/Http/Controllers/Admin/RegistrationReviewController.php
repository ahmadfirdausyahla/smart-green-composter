<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Neighborhood;
use App\Models\RegistrationRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RegistrationReviewController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = RegistrationRequest::with(['province', 'city', 'district', 'village', 'reviewer'])
            ->latest();

        // Admin Kota only sees requests for their city
        if ($user->isAdminKota()) {
            $query->where('city_id', $user->city_id);
        }

        $requests = $query->paginate(15);

        return view('admin.registration_requests.index', compact('requests', 'user'));
    }

    public function approve(Request $request, RegistrationRequest $registrationRequest): RedirectResponse
    {
        $user = $request->user();

        // Check jurisdiction
        if (! $user->isMasterAdmin() && (int) $registrationRequest->city_id !== (int) $user->city_id) {
            abort(403, 'Akses ditolak: Pendaftaran ini berada di luar yurisdiksi Kota Anda.');
        }

        if ($registrationRequest->status !== 'pending') {
            return back()->with('error', 'Permohonan ini telah direspons sebelumnya.');
        }

        DB::transaction(function () use ($registrationRequest, $user) {
            // Find or create neighborhood
            $neighborhood = Neighborhood::firstOrCreate(
                [
                    'village_id' => $registrationRequest->village_id,
                    'rt_number' => $registrationRequest->rt_number,
                    'rw_number' => $registrationRequest->rw_number,
                ],
                [
                    'name' => "RT {$registrationRequest->rt_number} / RW {$registrationRequest->rw_number}",
                    'address' => $registrationRequest->supporting_info,
                ]
            );

            // Create active User account for RT/RW
            $newUser = User::create([
                'name' => $registrationRequest->name,
                'email' => $registrationRequest->email,
                'password' => $registrationRequest->password, // Already hashed on registration
                'phone_number' => $registrationRequest->phone_number,
                'role' => UserRole::ADMIN_RTRW,
                'city_id' => $registrationRequest->city_id,
                'neighborhood_id' => $neighborhood->id,
                'status' => 'active',
            ]);

            $registrationRequest->update([
                'status' => 'approved',
                'reviewed_by' => $user->id,
                'reviewed_at' => now(),
            ]);

            AuditLog::record(
                action: 'RTRW_REGISTRATION_APPROVED',
                description: "Permohonan pendaftaran RT {$registrationRequest->rt_number}/RW {$registrationRequest->rw_number} disetujui oleh {$user->name}. Akun {$newUser->email} diaktifkan.",
                entityType: 'RegistrationRequest',
                entityId: $registrationRequest->id
            );
        });

        return back()->with('success', "Pendaftaran {$registrationRequest->name} berhasil disetujui. Akun Admin RT/RW telah aktif.");
    }

    public function reject(Request $request, RegistrationRequest $registrationRequest): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isMasterAdmin() && (int) $registrationRequest->city_id !== (int) $user->city_id) {
            abort(403);
        }

        $validated = $request->validate([
            'review_notes' => ['required', 'string', 'max:500'],
        ]);

        $registrationRequest->update([
            'status' => 'rejected',
            'review_notes' => $validated['review_notes'],
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);

        AuditLog::record(
            action: 'RTRW_REGISTRATION_REJECTED',
            description: "Permohonan pendaftaran dari {$registrationRequest->name} ditolak oleh {$user->name}. Alasan: {$validated['review_notes']}",
            entityType: 'RegistrationRequest',
            entityId: $registrationRequest->id
        );

        return back()->with('success', "Pendaftaran {$registrationRequest->name} telah ditolak.");
    }
}
