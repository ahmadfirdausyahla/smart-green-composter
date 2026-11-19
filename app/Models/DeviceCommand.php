<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceCommand extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'command_type',
        'parameters',
        'issued_by',
        'status',
        'sent_at',
        'acknowledged_at',
        'response_payload',
    ];

    protected function casts(): array
    {
        return [
            'parameters' => 'array',
            'sent_at' => 'datetime',
            'acknowledged_at' => 'datetime',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}
