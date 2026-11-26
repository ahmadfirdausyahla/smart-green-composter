<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceCommand;
use App\Services\IoT\IoTService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceTelemetryController extends Controller
{
    /**
     * Ingest telemetry from ESP32.
     * Authenticated via API key in header or request body.
     */
    public function ingest(Request $request, IoTService $iotService): JsonResponse
    {
        $apiKey = $request->header('X-Device-Key') ?? $request->input('api_key');

        if (! $apiKey) {
            return response()->json(['error' => 'API Key wajib disertakan pada header X-Device-Key.'], 401);
        }

        $device = Device::where('api_key', $apiKey)->first();
        if (! $device) {
            return response()->json(['error' => 'Perangkat tidak terdaftar.'], 403);
        }

        // Process telemetry using IoT Provider abstraction layer
        $reading = $iotService->processTelemetry($device, $request->all());

        // Check if there is any pending queued command for this device
        $pendingCommand = DeviceCommand::where('device_id', $device->id)
            ->where('status', 'queued')
            ->oldest()
            ->first();

        $cmdPayload = null;
        if ($pendingCommand) {
            $pendingCommand->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            $cmdPayload = [
                'command_id' => $pendingCommand->id,
                'type' => $pendingCommand->command_type,
                'parameters' => $pendingCommand->parameters,
            ];
        }

        return response()->json([
            'status' => 'success',
            'device_code' => $device->device_code,
            'server_time' => now()->timestamp,
            'reading' => $reading->toArray(),
            'pending_command' => $cmdPayload,
        ]);
    }

    /**
     * ESP32 Command Execution Acknowledgment.
     */
    public function acknowledgeCommand(Request $request): JsonResponse
    {
        $apiKey = $request->header('X-Device-Key') ?? $request->input('api_key');
        $device = Device::where('api_key', $apiKey)->first();

        if (! $device) {
            return response()->json(['error' => 'Perangkat tidak terdaftar.'], 403);
        }

        $cmdId = $request->input('command_id');
        $command = DeviceCommand::where('device_id', $device->id)->find($cmdId);

        if (! $command) {
            return response()->json(['error' => 'Perintah tidak ditemukan.'], 404);
        }

        $command->update([
            'status' => $request->input('status', 'executed') === 'executed' ? 'executed' : 'failed',
            'acknowledged_at' => now(),
            'response_payload' => json_encode($request->all()),
        ]);

        return response()->json([
            'status' => 'acknowledged',
            'command_id' => $command->id,
        ]);
    }
}
