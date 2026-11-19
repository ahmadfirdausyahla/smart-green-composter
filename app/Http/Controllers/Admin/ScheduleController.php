<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // 1. Fetch operators list for RT/RW to assign
        $operators = collect();
        if ($user->isAdminRtRw()) {
            $operators = User::where('role', UserRole::ADMIN_LAPANGAN)
                ->where('neighborhood_id', $user->neighborhood_id)
                ->get();
        }

        // 2. Fetch schedules
        $query = Schedule::with(['operator', 'creator', 'neighborhood.village'])->orderBy('duty_date', 'asc');

        if ($user->isAdminLapangan()) {
            // Field operator only sees their own assigned schedules
            $query->where('operator_id', $user->id);
        } elseif ($user->isAdminRtRw()) {
            // RT/RW admin sees schedules in their neighborhood
            $query->where('neighborhood_id', $user->neighborhood_id);
        } elseif ($user->isAdminKota()) {
            $query->whereHas('neighborhood.village.district', function ($q) use ($user) {
                $q->where('city_id', $user->city_id);
            });
        }

        $schedules = $query->paginate(15);

        return view('admin.schedules.index', compact('schedules', 'operators', 'user'));
    }

    /**
     * Admin RT/RW: Create new operator schedule.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isAdminRtRw()) {
            abort(403);
        }

        $validated = $request->validate([
            'operator_id' => ['required', 'exists:users,id'],
            'duty_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required'],
            'end_time' => ['required', 'after:start_time'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $schedule = Schedule::create([
            'neighborhood_id' => $user->neighborhood_id,
            'operator_id' => $validated['operator_id'],
            'created_by' => $user->id,
            'duty_date' => $validated['duty_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $operator = User::find($validated['operator_id']);

        AuditLog::record(
            action: 'DUTY_SCHEDULE_CREATED',
            description: "Admin RT/RW {$user->name} membuat jadwal tugas untuk {$operator->name} pada tanggal {$validated['duty_date']}.",
            entityType: 'Schedule',
            entityId: $schedule->id
        );

        return back()->with('success', "Jadwal tugas untuk {$operator->name} berhasil disimpan.");
    }

    /**
     * Admin RT/RW: Delete a schedule.
     */
    public function destroy(Request $request, Schedule $schedule): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isAdminRtRw() || (int) $schedule->neighborhood_id !== (int) $user->neighborhood_id) {
            abort(403);
        }

        $schedule->delete();

        return back()->with('success', 'Jadwal operasional berhasil dihapus.');
    }

    /**
     * Admin RT/RW: Register a new Petugas Lapangan account.
     */
    public function createOperator(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isAdminRtRw()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'phone_number' => ['required', 'string', 'max:20'],
        ]);

        $operator = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone_number' => $validated['phone_number'],
            'role' => UserRole::ADMIN_LAPANGAN,
            'city_id' => $user->city_id,
            'neighborhood_id' => $user->neighborhood_id,
            'status' => 'active',
        ]);

        AuditLog::record(
            action: 'FIELD_OPERATOR_CREATED',
            description: "Admin RT/RW {$user->name} membuat akun Petugas Lapangan baru: {$operator->name} ({$operator->email}).",
            entityType: 'User',
            entityId: $operator->id
        );

        return back()->with('success', "Akun Petugas Lapangan ({$operator->name}) berhasil dibuat dan langsung aktif.");
    }
}
