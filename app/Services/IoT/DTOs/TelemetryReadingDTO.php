<?php

namespace App\Services\IoT\DTOs;

class TelemetryReadingDTO
{
    public function __construct(
        public readonly int|string $deviceId,
        public readonly string $deviceCode,
        public readonly string $status,
        public readonly float $temperature,
        public readonly float $weight,
        public readonly string $connectionStatus,
        public readonly int $timerRemainingSec,
        public readonly float $minTemperature,
        public readonly float $maxTemperature,
        public readonly string $lastUpdated,
        public readonly bool $isSimulated = true,
        public readonly array $rawPayload = []
    ) {}

    public function toArray(): array
    {
        return [
            'device_id' => $this->deviceId,
            'device_code' => $this->deviceCode,
            'status' => $this->status,
            'temperature' => $this->temperature,
            'weight' => $this->weight,
            'connection_status' => $this->connectionStatus,
            'timer_remaining' => $this->timerRemainingSec,
            'min_temperature' => $this->minTemperature,
            'max_temperature' => $this->maxTemperature,
            'last_updated' => $this->lastUpdated,
            'is_simulated' => $this->isSimulated,
        ];
    }
}
