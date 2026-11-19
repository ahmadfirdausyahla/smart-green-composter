<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IoTSimulatorController extends Controller
{
    public function index(Request $request): View
    {
        $devices = Device::with(['city', 'neighborhood', 'configuration'])->get();
        $selectedDeviceId = $request->query('device_id');
        $selectedDevice = $selectedDeviceId ? $devices->firstWhere('id', (int) $selectedDeviceId) : $devices->first();

        return view('admin.iot_simulator.index', compact('devices', 'selectedDevice'));
    }
}
