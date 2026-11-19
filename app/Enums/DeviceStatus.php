<?php

namespace App\Enums;

enum DeviceStatus: string
{
    case OFFLINE = 'offline';
    case IDLE = 'idle';
    case RUNNING = 'running';
    case WARNING = 'warning';
    case CRITICAL = 'critical';

    public function label(): string
    {
        return match ($this) {
            self::OFFLINE => 'Offline',
            self::IDLE => 'Standby / Siap',
            self::RUNNING => 'Sedang Beroperasi',
            self::WARNING => 'Peringatan Suhu/Sensor',
            self::CRITICAL => 'Kritis / Emergency Cutoff',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::OFFLINE => 'bg-slate-100 text-slate-600 border-slate-200',
            self::IDLE => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::RUNNING => 'bg-blue-50 text-blue-700 border-blue-200',
            self::WARNING => 'bg-amber-50 text-amber-700 border-amber-200',
            self::CRITICAL => 'bg-rose-50 text-rose-700 border-rose-200',
        };
    }
}
