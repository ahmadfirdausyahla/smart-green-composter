<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\City;
use App\Models\Province;
use App\Models\RegistrationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterRtRwController extends Controller
{
    public function showRegistrationForm(): View
    {
        $provinces = Province::with(['cities.districts.villages'])->get();
        return view('auth.register-rtrw', compact('provinces'));
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email', 'unique:registration_requests,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone_number' => ['required', 'string', 'max:20'],
            'province_id' => ['required', 'exists:provinces,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'district_id' => ['required', 'exists:districts,id'],
            'village_id' => ['required', 'exists:villages,id'],
            'rt_number' => ['required', 'string', 'max:10'],
            'rw_number' => ['required', 'string', 'max:10'],
            'supporting_info' => ['nullable', 'string', 'max:1000'],
        ]);

        $regRequest = RegistrationRequest::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone_number' => $validated['phone_number'],
            'province_id' => $validated['province_id'],
            'city_id' => $validated['city_id'],
            'district_id' => $validated['district_id'],
            'village_id' => $validated['village_id'],
            'rt_number' => $validated['rt_number'],
            'rw_number' => $validated['rw_number'],
            'supporting_info' => $validated['supporting_info'] ?? null,
            'status' => 'pending',
        ]);

        AuditLog::record(
            action: 'RTRW_REGISTRATION_SUBMITTED',
            description: "Pendaftaran akun Admin RT {$validated['rt_number']}/RW {$validated['rw_number']} diajukan oleh {$validated['name']}.",
            entityType: 'RegistrationRequest',
            entityId: $regRequest->id
        );

        return redirect()->route('login')->with('success', 'Permohonan pendaftaran Admin RT/RW berhasil dikirim. Akun Anda akan diverifikasi oleh Admin Kota terkait.');
    }
}
