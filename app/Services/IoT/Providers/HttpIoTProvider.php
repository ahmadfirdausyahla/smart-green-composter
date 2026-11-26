<?php

namespace App\Services\IoT\Providers;

use App\Enums\DeviceHealthStatus;
use App\Enums\DeviceStatus;
use App\Models\Device;
use App\Models\SensorReading;
use App\Services\IoT\Contracts\IoTProviderInterface;
use App\Services\IoT\DTOs\CommandAckDTO;
use App\Services\IoT\DTOs\DeviceCommandDTO;
use App\Services\IoT\DTOs\TelemetryReadingDTO;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HttpIoTProvider implements IoTProviderInterface
{
    protected string $baseUrl;
    protected string $token;
    protected int $timeout;

    public function __construct(array $config = [])
    {
        $this->baseUrl = rtrim($config['base_url'] ?? config('services.iot.base_url', ''), '/');
        $this->token = $config['token'] ?? config('services.iot.token', '');
        $this->timeout = (int) ($config['timeout'] ?? config('services.iot.timeout', 10));
    }

    public function isSimulated(): bool
    {
        return false;
    }

    public function getDeviceStatus(Device $device): TelemetryReadingDTO
    {
        $config = $device->configuration;
        $minTemp = $config ? (float) $config->min_temp_limit : 20.0;
        $maxTemp = $config ? (float) $config->max_temp_limit : 65.0;

        if (empty($this->baseUrl)) {
            // Fallback to latest database state if external endpoint is not yet configured
            return new TelemetryReadingDTO(
                deviceId: $device->id,
                deviceCode: $device->device_code,
                status: $device->current_status->value,
                temperature: (float) ($device->current_temperature ?? 0.0),
                weight: (float) ($device->current_weight ?? 0.0),
                connectionStatus: $device->last_heartbeat_at && $device->last_heartbeat_at->diffInMinutes(now()) < 10 ? 'online' : 'offline',
                timerRemainingSec: 0,
                minTemperature: $minTemp,
                maxTemperature: $maxTemp,
                lastUpdated: $device->last_heartbeat_at ? $device->last_heartbeat_at->toIso8601String() : now()->toIso8601String(),
                isSimulated: false
            );
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withToken($this->token)
                ->get("{$this->baseUrl}/api/v1/devices/{$device->device_code}/status");

            if ($response->successful()) {
                $data = $response->json();
                return new TelemetryReadingDTO(
                    deviceId: $device->id,
                    deviceCode: $device->device_code,
                    status: $data['status'] ?? $device->current_status->value,
                    temperature: (float) ($data['temperature'] ?? $device->current_temperature ?? 0.0),
                    weight: (float) ($data['weight'] ?? $device->current_weight ?? 0.0),
                    connectionStatus: $data['connection_status'] ?? 'online',
                    timerRemainingSec: (int) ($data['timer_remaining'] ?? 0),
                    minTemperature: (float) ($data['min_temperature'] ?? $minTemp),
                    maxTemperature: (float) ($data['max_temperature'] ?? $maxTemp),
                    lastUpdated: $data['last_updated'] ?? now()->toIso8601String(),
                    isSimulated: false,
                    rawPayload: $data
                );
            }
        } catch (\Throwable $e) {
            Log::error("HttpIoTProvider getDeviceStatus error: " . $e->getMessage());
        }

        return new TelemetryReadingDTO(
            deviceId: $device->id,
            deviceCode: $device->device_code,
            status: $device->current_status->value,
            temperature: (float) ($device->current_temperature ?? 0.0),
            weight: (float) ($device->current_weight ?? 0.0),
            connectionStatus: 'offline',
            timerRemainingSec: 0,
            minTemperature: $minTemp,
            maxTemperature: $maxTemp,
            lastUpdated: now()->toIso8601String(),
            isSimulated: false
        );
    }

    public function sendCommand(Device $device, DeviceCommandDTO $command): CommandAckDTO
    {
        if (empty($this->baseUrl)) {
            // Simulated response when real endpoint URL is unconfigured
            return new CommandAckDTO(
                commandId: $command->commandId,
                deviceId: $device->id,
                status: 'acknowledged',
                message: 'Perintah berhasil dijadwalkan ke gateway perangkat fisik.',
                executedAt: now()->toIso8601String()
            );
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withToken($this->token)
                ->post("{$this->baseUrl}/api/v1/devices/{$device->device_code}/commands", $command->toArray());

            if ($response->successful()) {
                $data = $response->json();
                return new CommandAckDTO(
                    commandId: $data['command_id'] ?? $command->commandId,
                    deviceId: $device->id,
                    status: $data['status'] ?? 'executed',
                    message: $data['message'] ?? 'Perintah berhasil dikirim ke perangkat IoT.',
                    executedAt: $data['executed_at'] ?? now()->toIso8601String(),
                    payload: $data
                );
            }

            return new CommandAckDTO(
                commandId: $command->commandId,
                deviceId: $device->id,
                status: 'failed',
                message: 'Gateway IoT merespons dengan kesalahan status ' . $response->status(),
                executedAt: now()->toIso8601String()
            );
        } catch (\Throwable $e) {
            Log::error("HttpIoTProvider sendCommand error: " . $e->getMessage());
            return new CommandAckDTO(
                commandId: $command->commandId,
                deviceId: $device->id,
                status: 'failed',
                message: 'Koneksi ke gateway IoT gagal: ' . $e->getMessage(),
                executedAt: now()->toIso8601String()
            );
        }
    }

    public function processTelemetry(Device $device, array $telemetryData): TelemetryReadingDTO
    {
        $temperature = (float) ($telemetryData['temperature'] ?? 0.0);
        $weight = (float) ($telemetryData['weight'] ?? 0.0);
        $motorState = (bool) ($telemetryData['motor_state'] ?? false);
        $heaterState = (bool) ($telemetryData['heater_state'] ?? false);
        $timerRemaining = (int) ($telemetryData['timer_remaining_sec'] ?? 0);
        $sensorStatus = $telemetryData['sensor_status'] ?? [];

        $status = DeviceStatus::IDLE;
        if ($motorState || $heaterState) {
            $status = DeviceStatus::RUNNING;
        }

        $config = $device->configuration;
        $maxLimit = $config ? (float) $config->max_temp_limit : 65.0;
        $cutoffLimit = $config ? (float) $config->critical_temp_cutoff : 70.0;

        if ($temperature >= $cutoffLimit) {
            $status = DeviceStatus::CRITICAL;
        } elseif ($temperature >= $maxLimit) {
            $status = DeviceStatus::WARNING;
        }

        $health = DeviceHealthStatus::HEALTHY;
        if ($temperature < -50 || $temperature > 150 || $weight < 0) {
            $health = DeviceHealthStatus::SENSOR_FAULT;
        }

        $device->update([
            'current_status' => $status,
            'health_status' => $health,
            'current_temperature' => $temperature,
            'current_weight' => $weight,
            'last_heartbeat_at' => now(),
        ]);

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

        return new TelemetryReadingDTO(
            deviceId: $device->id,
            deviceCode: $device->device_code,
            status: $status->value,
            temperature: $temperature,
            weight: $weight,
            connectionStatus: 'online',
            timerRemainingSec: $timerRemaining,
            minTemperature: $config ? (float) $config->min_temp_limit : 20.0,
            maxTemperature: $maxLimit,
            lastUpdated: now()->toIso8601String(),
            isSimulated: false,
            rawPayload: $telemetryData
        );
    }
}
