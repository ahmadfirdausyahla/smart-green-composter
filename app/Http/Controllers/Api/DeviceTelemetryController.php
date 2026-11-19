<?php

namespace App\Http\Controllers\Api;

use App\Enums\DeviceHealthStatus;
use App\Enums\DeviceStatus;
use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceCommand;
use App\Models\SensorReading;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceTelemetryController extends Controller
{
    /**
     * Ingest telemetry from ESP32.
     * Authenticated via API key in header or request body.
     */
    public function ingest(Request $request): JsonResponse
    {
        $apiKey = $request->header('X-Device-Key') ?? $request->input('api_key');

        if (! $apiKey) {
            return response()->json(['error' => 'API Key wajib disertakan pada header X-Device-Key.'], 401);
        }

        $device = Device::where('api_key', $apiKey)->first();
        if (! $device) {
            return response()->json(['error' => 'Perangkat tidak terdaftar.'], 403);
        }

        $temperature = (float) $request->input('temperature', 0.0);
        $weight = (float) $request->input('weight', 0.0);
        $motorState = (bool) $request->input('motor_state', false);
        $heaterState = (bool) $request->input('heater_state', false);
        $timerRemaining = (int) $request->input('timer_remaining_sec', 0);
        $sensorStatus = $request->input('sensor_status', []);

        // Status Determination
        $status = DeviceStatus::IDLE;
        if ($motorState || $heaterState) {
            $status = DeviceStatus::RUNNING;
        }

        // Safety Threshold Check (Overheat Warning)
        $config = $device->configuration;
        $maxLimit = $config ? $config->max_temp_limit : 65.0;
        $cutoffLimit = $config ? $config->critical_temp_cutoff : 70.0;

        if ($temperature >= $cutoffLimit) {
            $status = DeviceStatus::CRITICAL;
        } elseif ($temperature >= $maxLimit) {
            $status = DeviceStatus::WARNING;
        }

        // Sensor Fault Detection
        $health = DeviceHealthStatus::HEALTHY;
        if ($temperature < -50 || $temperature > 150 || $weight < 0) {
            $health = DeviceHealthStatus::SENSOR_FAULT;
        }

        // Update Device State
        $device->update([
            'current_status' => $status,
            'health_status' => $health,
            'current_temperature' => $temperature,
            'current_weight' => $weight,
            'last_heartbeat_at' => now(),
        ]);

        // Record Sensor History
        SensorReading::create([
            'device_id' => $device->id,
            'temperature' => $temperature,
            'weight' => $weight,
            'motor_running' => $motorState,
            'heater_running' => $heaterState,
            'timer_remaining_sec' => $timerRemaining,
            'sensor_status' => $sensorStatus,
            'recorded_at' => now(),
        ]);

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
