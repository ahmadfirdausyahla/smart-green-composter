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

/**
 * [DATA DUMMY / SIMULASI IoT]
 * Provider ini menghasilkan data telemetri simulasi (suhu float °C, berat float Kg, timer int detik, status enum).
 * Saat integrasi perangkat keras ESP32/server IoT asli siap, ubah IOT_DRIVER=api di file .env
 * agar sistem otomatis beralih ke HttpIoTProvider tanpa mengubah logika aplikasi.
 */
class MockIoTProvider implements IoTProviderInterface
{
    public function isSimulated(): bool
    {
        return true;
    }

    public function getDeviceStatus(Device $device): TelemetryReadingDTO
    {
        $config = $device->configuration;
        $minTemp = $config ? (float) $config->min_temp_limit : 20.0;
        $maxTemp = $config ? (float) $config->max_temp_limit : 65.0;

        // Simulate realistic values based on current status
        $currentTemp = (float) ($device->current_temperature ?? 42.0);
        $currentWeight = (float) ($device->current_weight ?? 15.0);
        $status = $device->current_status ?? DeviceStatus::IDLE;

        // Dynamic fluctuations for realistic mock
        if ($status === DeviceStatus::RUNNING) {
            $currentTemp = min(64.5, $currentTemp + mt_rand(1, 5) * 0.1);
            $currentWeight = max(0.5, $currentWeight - 0.01);
            $timerRemaining = 7200;
        } else {
            $currentTemp = max(26.0, $currentTemp - 0.2);
            $timerRemaining = 0;
        }

        $connectionStatus = $device->last_heartbeat_at && $device->last_heartbeat_at->diffInMinutes(now()) < 15
            ? 'online'
            : 'online'; // Mock is online by default

        return new TelemetryReadingDTO(
            deviceId: $device->id,
            deviceCode: $device->device_code,
            status: $status->value,
            temperature: round($currentTemp, 1),
            weight: round($currentWeight, 2),
            connectionStatus: $connectionStatus,
            timerRemainingSec: $timerRemaining,
            minTemperature: $minTemp,
            maxTemperature: $maxTemp,
            lastUpdated: now()->toIso8601String(),
            isSimulated: true
        );
    }

    public function sendCommand(Device $device, DeviceCommandDTO $command): CommandAckDTO
    {
        $config = $device->configuration;
        $cutoffLimit = $config ? (float) $config->critical_temp_cutoff : 70.0;
        $commandName = strtoupper($command->command);

        // Simulate Command Execution
        if (in_array($commandName, ['POWER_ON', 'START'])) {
            $device->update([
                'current_status' => DeviceStatus::RUNNING,
                'health_status' => DeviceHealthStatus::HEALTHY,
                'last_heartbeat_at' => now(),
            ]);

            return new CommandAckDTO(
                commandId: $command->commandId,
                deviceId: $device->id,
                status: 'executed',
                message: 'Mesin berhasil dinyalakan (Simulasi Mock IoT).',
                executedAt: now()->toIso8601String(),
                payload: ['timer_minutes' => $command->parameters['timer_minutes'] ?? 240]
            );
        }

        if (in_array($commandName, ['POWER_OFF', 'STOP'])) {
            $device->update([
                'current_status' => DeviceStatus::IDLE,
                'last_heartbeat_at' => now(),
            ]);

            return new CommandAckDTO(
                commandId: $command->commandId,
                deviceId: $device->id,
                status: 'executed',
                message: 'Mesin berhasil dihentikan (Simulasi Mock IoT).',
                executedAt: now()->toIso8601String()
            );
        }

        if ($commandName === 'EMERGENCY_STOP') {
            $device->update([
                'current_status' => DeviceStatus::CRITICAL,
                'health_status' => DeviceHealthStatus::MECHANICAL_FAULT,
                'last_heartbeat_at' => now(),
            ]);

            return new CommandAckDTO(
                commandId: $command->commandId,
                deviceId: $device->id,
                status: 'executed',
                message: 'Emergency Stop dieksekusi seketika pada mesin.',
                executedAt: now()->toIso8601String()
            );
        }

        if ($commandName === 'SET_TIMER') {
            return new CommandAckDTO(
                commandId: $command->commandId,
                deviceId: $device->id,
                status: 'executed',
                message: 'Timer berhasil diperbarui.',
                executedAt: now()->toIso8601String(),
                payload: ['timer_minutes' => $command->parameters['timer_minutes'] ?? 240]
            );
        }

        if ($commandName === 'SET_TEMP_LIMIT') {
            if ($config) {
                $config->update([
                    'min_temp_limit' => $command->parameters['min_temp'] ?? $config->min_temp_limit,
                    'max_temp_limit' => $command->parameters['max_temp'] ?? $config->max_temp_limit,
                ]);
            }

            return new CommandAckDTO(
                commandId: $command->commandId,
                deviceId: $device->id,
                status: 'executed',
                message: 'Batas suhu bawah dan atas berhasil dikonfigurasi.',
                executedAt: now()->toIso8601String()
            );
        }

        return new CommandAckDTO(
            commandId: $command->commandId,
            deviceId: $device->id,
            status: 'failed',
            message: "Perintah {$commandName} tidak dikenali.",
            executedAt: now()->toIso8601String()
        );
    }

    public function processTelemetry(Device $device, array $telemetryData): TelemetryReadingDTO
    {
        $temperature = (float) ($telemetryData['temperature'] ?? 0.0);
        $weight = (float) ($telemetryData['weight'] ?? 0.0);
        $motorState = (bool) ($telemetryData['motor_state'] ?? false);
        $heaterState = (bool) ($telemetryData['heater_state'] ?? false);
        $timerRemaining = (int) ($telemetryData['timer_remaining_sec'] ?? 0);
        $sensorStatus = $telemetryData['sensor_status'] ?? [];

        // Status Determination
        $status = DeviceStatus::IDLE;
        if ($motorState || $heaterState) {
            $status = DeviceStatus::RUNNING;
        }

        // Safety Threshold Check
        $config = $device->configuration;
        $maxLimit = $config ? (float) $config->max_temp_limit : 65.0;
        $cutoffLimit = $config ? (float) $config->critical_temp_cutoff : 70.0;

        if ($temperature >= $cutoffLimit) {
            $status = DeviceStatus::CRITICAL;
        } elseif ($temperature >= $maxLimit) {
            $status = DeviceStatus::WARNING;
        }

        // Sensor Health
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
            isSimulated: true,
            rawPayload: $telemetryData
        );
    }
}
