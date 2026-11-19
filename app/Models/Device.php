<?php

namespace App\Models;

use App\Enums\DeviceHealthStatus;
use App\Enums\DeviceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial_number',
        'device_code',
        'model_name',
        'api_key',
        'city_id',
        'neighborhood_id',
        'current_status',
        'health_status',
        'current_temperature',
        'current_weight',
        'last_heartbeat_at',
        'installed_at',
        'is_publicly_visible',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'current_status' => DeviceStatus::class,
            'health_status' => DeviceHealthStatus::class,
            'current_temperature' => 'float',
            'current_weight' => 'float',
            'last_heartbeat_at' => 'datetime',
            'installed_at' => 'datetime',
            'is_publicly_visible' => 'boolean',
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

    public function configuration(): HasOne
    {
        return $this->hasOne(DeviceConfiguration::class);
    }

    public function operationSessions(): HasMany
    {
        return $this->hasMany(OperationSession::class);
    }

    public function commands(): HasMany
    {
        return $this->hasMany(DeviceCommand::class);
    }

    public function sensorReadings(): HasMany
    {
        return $this->hasMany(SensorReading::class);
    }

    public function maintenanceReports(): HasMany
    {
        return $this->hasMany(MaintenanceReport::class);
    }

    public function operationPermissions(): HasMany
    {
        return $this->hasMany(OperationPermission::class);
    }

    // Regional Scope Helpers
    public function scopeForUser($query, User $user)
    {
        if ($user->isMasterAdmin()) {
            return $query;
        }

        if ($user->isAdminKota()) {
            return $query->where('city_id', $user->city_id);
        }

        if ($user->isAdminRtRw() || $user->isAdminLapangan()) {
            return $query->where('neighborhood_id', $user->neighborhood_id);
        }

        return $query->whereRaw('1 = 0');
    }
}
