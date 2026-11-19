<?php

namespace App\Enums;

enum DeviceHealthStatus: string
{
    case HEALTHY = 'healthy';
    case SENSOR_FAULT = 'sensor_fault';
    case MECHANICAL_FAULT = 'mechanical_fault';

    public function label(): string
    {
        return match ($this) {
            self::HEALTHY => 'Kondisi Normal / Sehat',
            self::SENSOR_FAULT => 'Gangguan Sensor',
            self::MECHANICAL_FAULT => 'Gangguan Mekanis / Motor',
        };
    }
}
