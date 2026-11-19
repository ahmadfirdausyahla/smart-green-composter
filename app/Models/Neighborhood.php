<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Neighborhood extends Model
{
    use HasFactory;

    protected $fillable = [
        'village_id',
        'rt_number',
        'rw_number',
        'name',
        'address',
        'latitude',
        'longitude',
    ];

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function getFullLabelAttribute(): string
    {
        $vName = $this->village?->name ?? '';
        $dName = $this->village?->district?->name ?? '';
        $cName = $this->village?->district?->city?->name ?? '';
        return "RT {$this->rt_number} / RW {$this->rw_number}, {$vName}, {$dName}, {$cName}";
    }
}
