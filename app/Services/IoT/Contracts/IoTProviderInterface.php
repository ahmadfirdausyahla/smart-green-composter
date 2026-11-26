<?php

namespace App\Services\IoT\Contracts;

use App\Models\Device;
use App\Services\IoT\DTOs\CommandAckDTO;
use App\Services\IoT\DTOs\DeviceCommandDTO;
use App\Services\IoT\DTOs\TelemetryReadingDTO;

interface IoTProviderInterface
{
    /**
     * Retrieve the latest telemetry / status reading for a device.
     */
    public function getDeviceStatus(Device $device): TelemetryReadingDTO;

    /**
     * Dispatch an operational command to the device hardware / IoT gateway.
     */
    public function sendCommand(Device $device, DeviceCommandDTO $command): CommandAckDTO;

    /**
     * Ingest and process telemetry data from an incoming payload.
     */
    public function processTelemetry(Device $device, array $telemetryData): TelemetryReadingDTO;

    /**
     * Check if this provider is a simulated mock provider.
     */
    public function isSimulated(): bool;
}
