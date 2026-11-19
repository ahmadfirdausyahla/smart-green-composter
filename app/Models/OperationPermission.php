<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'requester_id',
        'approver_id',
        'action_type',
        'duration_minutes_allowed',
        'status',
        'requested_at',
        'responded_at',
        'expires_at',
        'rejection_reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'requested_at' => 'datetime',
            'responded_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function isValidNow(): bool
    {
        return $this->status === 'approved' &&
               $this->expires_at &&
               $this->expires_at->isFuture();
    }
}
