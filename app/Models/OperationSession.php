<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OperationSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_uuid',
        'device_id',
        'operator_id',
        'permission_id',
        'initial_weight',
        'final_weight',
        'weight_reduction_kg',
        'reduction_percentage',
        'target_duration_minutes',
        'actual_duration_minutes',
        'peak_temperature',
        'avg_temperature',
        'started_at',
        'ended_at',
        'status',
        'failure_reason',
    ];

    protected function casts(): array
    {
        return [
            'initial_weight' => 'float',
            'final_weight' => 'float',
            'weight_reduction_kg' => 'float',
            'reduction_percentage' => 'float',
            'peak_temperature' => 'float',
            'avg_temperature' => 'float',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(OperationPermission::class);
    }

    public function sensorReadings(): HasMany
    {
        return $this->hasMany(SensorReading::class);
    }

    /**
     * Compute mass reduction and percentage cleanly.
     */
    public function recordFinalWeight(float $finalWeight, ?float $peakTemp = null, ?float $avgTemp = null): void
    {
        $this->final_weight = $finalWeight;
        $diff = max(0, $this->initial_weight - $finalWeight);
        $this->weight_reduction_kg = round($diff, 2);

        if ($this->initial_weight > 0) {
            $this->reduction_percentage = round(($diff / $this->initial_weight) * 100, 2);
        } else {
            $this->reduction_percentage = 0.00;
        }

        if ($peakTemp !== null) {
            $this->peak_temperature = $peakTemp;
        }
        if ($avgTemp !== null) {
            $this->avg_temperature = $avgTemp;
        }

        $this->ended_at = now();
        $this->actual_duration_minutes = (int) $this->started_at->diffInMinutes($this->ended_at);
        $this->status = 'completed_normally';
        $this->save();
    }
}
