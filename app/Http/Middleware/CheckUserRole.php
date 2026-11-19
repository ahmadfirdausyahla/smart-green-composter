<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->status !== 'active') {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Akun Anda sedang menunggu persetujuan atau dinonaktifkan.');
        }

        $userRoleValue = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;

        // If 'any_admin' is passed or user role matches any allowed role
        if (in_array($userRoleValue, $roles, true)) {
            return $next($request);
        }

        // Master admin has access to general admin resources
        if ($userRoleValue === UserRole::MASTER_ADMIN->value) {
            return $next($request);
        }

        abort(403, 'Akses ditolak: Anda tidak memiliki wewenang untuk membuka halaman ini.');
    }
}
