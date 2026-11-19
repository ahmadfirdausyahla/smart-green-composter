<?php

namespace App\Http\Middleware;

use App\Models\Device;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceRegionalBoundary
{
    /**
     * Handle an incoming request.
     * Enforce strict regional boundaries to prevent horizontal privilege escalation.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Master Admin can access all regions
        if ($user->isMasterAdmin()) {
            return $next($request);
        }

        // Check if route has a device model or parameter
        $device = $request->route('device');
        if ($device) {
            if (! ($device instanceof Device)) {
                $device = Device::find($device);
            }

            if ($device) {
                if ($user->isAdminKota()) {
                    if ((int) $device->city_id !== (int) $user->city_id) {
                        abort(403, 'Akses ditolak: Perangkat ini berada di luar yurisdiksi Kota Anda.');
                    }
                } elseif ($user->isAdminRtRw() || $user->isAdminLapangan()) {
                    if ((int) $device->neighborhood_id !== (int) $user->neighborhood_id) {
                        abort(403, 'Akses ditolak: Perangkat ini bukan milik wilayah RT/RW Anda.');
                    }
                }
            }
        }

        return $next($request);
    }
}
