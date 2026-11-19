<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'operation_session_id',
        'temperature',
        'weight',
        'motor_running',
        'heater_running',
        'timer_remaining_sec',
        'sensor_status',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'temperature' => 'float',
            'weight' => 'float',
            'motor_running' => 'boolean',
            'heater_running' => 'boolean',
            'timer_remaining_sec' => 'integer',
            'sensor_status' => 'array',
            'recorded_at' => 'datetime',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(OperationSession::class, 'operation_session_id');
    }
}
