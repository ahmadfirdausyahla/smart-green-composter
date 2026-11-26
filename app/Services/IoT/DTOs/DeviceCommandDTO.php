<?php

namespace App\Services\IoT\DTOs;

class DeviceCommandDTO
{
    public function __construct(
        public readonly string|int $commandId,
        public readonly string|int $deviceId,
        public readonly string $command,
        public readonly array $parameters = [],
        public readonly ?int $requestedBy = null,
        public readonly ?string $createdAt = null
    ) {}

    public function toArray(): array
    {
        return [
            'command_id' => $this->commandId,
            'device_id' => $this->deviceId,
            'command' => $this->command,
            'parameters' => $this->parameters,
            'requested_by' => $this->requestedBy,
            'created_at' => $this->createdAt ?? now()->toIso8601String(),
        ];
    }
}
