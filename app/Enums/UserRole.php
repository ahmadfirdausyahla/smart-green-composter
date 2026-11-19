<?php

namespace App\Enums;

enum UserRole: string
{
    case MASTER_ADMIN = 'master_admin';
    case ADMIN_KOTA = 'admin_kota';
    case ADMIN_RTRW = 'admin_rtrw';
    case ADMIN_LAPANGAN = 'admin_lapangan';

    public function label(): string
    {
        return match ($this) {
            self::MASTER_ADMIN => 'Master Admin',
            self::ADMIN_KOTA => 'Admin Kota/Kabupaten',
            self::ADMIN_RTRW => 'Admin RT/RW',
            self::ADMIN_LAPANGAN => 'Admin Lapangan / Petugas',
        };
    }
}
