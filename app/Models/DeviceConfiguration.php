<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'min_temp_limit',
        'max_temp_limit',
        'critical_temp_cutoff',
        'default_timer_minutes',
        'auto_shutdown_enabled',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'min_temp_limit' => 'float',
            'max_temp_limit' => 'float',
            'critical_temp_cutoff' => 'float',
            'default_timer_minutes' => 'integer',
            'auto_shutdown_enabled' => 'boolean',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
