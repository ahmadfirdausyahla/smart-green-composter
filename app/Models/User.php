<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'role',
        'city_id',
        'neighborhood_id',
        'status',
        'avatar_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function neighborhood(): BelongsTo
    {
        return $this->belongsTo(Neighborhood::class);
    }

    public function operatedSessions(): HasMany
    {
        return $this->hasMany(OperationSession::class, 'operator_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'operator_id');
    }

    public function requestedPermissions(): HasMany
    {
        return $this->hasMany(OperationPermission::class, 'requester_id');
    }

    public function approvedPermissions(): HasMany
    {
        return $this->hasMany(OperationPermission::class, 'approver_id');
    }

    public function maintenanceReports(): HasMany
    {
        return $this->hasMany(MaintenanceReport::class, 'reporter_id');
    }

    // Role Helper Methods
    public function isMasterAdmin(): bool
    {
        return $this->role === UserRole::MASTER_ADMIN;
    }

    public function isAdminKota(): bool
    {
        return $this->role === UserRole::ADMIN_KOTA;
    }

    public function isAdminRtRw(): bool
    {
        return $this->role === UserRole::ADMIN_RTRW;
    }

    public function isAdminLapangan(): bool
    {
        return $this->role === UserRole::ADMIN_LAPANGAN;
    }

    /**
     * Helper to get user's assigned jurisdiction name.
     */
    public function getJurisdictionNameAttribute(): string
    {
        if ($this->isMasterAdmin()) {
            return 'Seluruh Indonesia';
        }
        if ($this->isAdminKota()) {
            return $this->city ? $this->city->name : 'Kota Belum Diatur';
        }
        if ($this->isAdminRtRw() || $this->isAdminLapangan()) {
            if ($this->neighborhood) {
                return "RT {$this->neighborhood->rt_number} / RW {$this->neighborhood->rw_number} - " . ($this->neighborhood->village->name ?? '');
            }
            return 'Wilayah Belum Diatur';
        }
        return 'Umum';
    }
}
