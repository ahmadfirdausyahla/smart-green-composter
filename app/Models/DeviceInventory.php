<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_name',
        'central_stock',
        'city_id',
        'city_stock',
    ];

    protected function casts(): array
    {
        return [
            'central_stock' => 'integer',
            'city_stock' => 'integer',
        ];
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
