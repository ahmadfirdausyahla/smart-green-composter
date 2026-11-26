<?php

namespace App\Services\IoT;

use App\Models\Device;
use App\Services\IoT\Contracts\IoTProviderInterface;
use App\Services\IoT\DTOs\CommandAckDTO;
use App\Services\IoT\DTOs\DeviceCommandDTO;
use App\Services\IoT\DTOs\TelemetryReadingDTO;
use App\Services\IoT\Providers\HttpIoTProvider;
use App\Services\IoT\Providers\MockIoTProvider;

class IoTService
{
    protected IoTProviderInterface $provider;

    public function __construct(?IoTProviderInterface $provider = null)
    {
        if ($provider) {
            $this->provider = $provider;
            return;
        }

        $driver = config('services.iot.driver', env('IOT_DRIVER', 'mock'));

        $this->provider = match (strtolower($driver)) {
            'api', 'http' => new HttpIoTProvider(),
            default => new MockIoTProvider(),
        };
    }

    public function getProvider(): IoTProviderInterface
    {
        return $this->provider;
    }

    public function isMock(): bool
    {
        return $this->provider->isSimulated();
    }

    public function getDeviceStatus(Device $device): TelemetryReadingDTO
    {
        return $this->provider->getDeviceStatus($device);
    }

    public function sendCommand(Device $device, DeviceCommandDTO $command): CommandAckDTO
    {
        return $this->provider->sendCommand($device, $command);
    }

    public function processTelemetry(Device $device, array $telemetryData): TelemetryReadingDTO
    {
        return $this->provider->processTelemetry($device, $telemetryData);
    }
}
