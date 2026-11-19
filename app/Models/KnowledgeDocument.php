<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KnowledgeDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'source_type',
        'content',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function chunks(): HasMany
    {
        return $this->hasMany(KnowledgeChunk::class, 'document_id');
    }
}
