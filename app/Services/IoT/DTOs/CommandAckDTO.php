<?php

namespace App\Services\IoT\DTOs;

class CommandAckDTO
{
    public function __construct(
        public readonly string|int $commandId,
        public readonly string|int $deviceId,
        public readonly string $status, // executed, acknowledged, failed, expired
        public readonly string $message,
        public readonly string $executedAt,
        public readonly array $payload = []
    ) {}

    public function toArray(): array
    {
        return [
            'command_id' => $this->commandId,
            'device_id' => $this->deviceId,
            'status' => $this->status,
            'message' => $this->message,
            'executed_at' => $this->executedAt,
            'payload' => $this->payload,
        ];
    }
}
