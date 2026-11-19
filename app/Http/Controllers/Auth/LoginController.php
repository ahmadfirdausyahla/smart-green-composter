<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->status !== 'active') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->with('error', 'Akun Anda sedang menunggu persetujuan Admin Kota atau dinonaktifkan.');
            }

            AuditLog::record(
                action: 'USER_LOGIN',
                description: "Pengguna {$user->name} ({$user->role->value}) berhasil login.",
                userId: $user->id
            );

            return $this->redirectBasedOnRole($user);
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            AuditLog::record(
                action: 'USER_LOGOUT',
                description: "Pengguna {$user->name} keluar dari sistem.",
                userId: $user->id
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }

    private function redirectBasedOnRole($user): RedirectResponse
    {
        // Admin Lapangan directly accesses Monitoring, others access Dashboard
        if ($user->isAdminLapangan()) {
            return redirect()->route('admin.monitoring.index');
        }

        return redirect()->route('admin.dashboard');
    }
}
