<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeChunk extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'chunk_text',
        'chunk_order',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'chunk_order' => 'integer',
            'metadata' => 'array',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(KnowledgeDocument::class, 'document_id');
    }
}
