<?php

namespace App\Services\AI;

use App\Models\KnowledgeChunk;
use App\Models\KnowledgeDocument;
use Illuminate\Support\Collection;

class KnowledgeBaseService
{
    /**
     * Search relevant knowledge base chunks using tokenized matching and scoring.
     */
    public function searchRelevantChunks(string $query, int $limit = 3): Collection
    {
        $cleanQuery = strtolower(trim($query));
        if ($cleanQuery === '') {
            return collect();
        }

        $keywords = array_filter(explode(' ', preg_replace('/[^\p{L}\p{N}\s]/u', '', $cleanQuery)));
        $chunks = KnowledgeChunk::with('document')->whereHas('document', function ($q) {
            $q->where('is_published', true);
        })->get();

        $scoredChunks = collect();

        foreach ($chunks as $chunk) {
            $chunkText = strtolower($chunk->chunk_text);
            $docTitle = strtolower($chunk->document?->title ?? '');
            $score = 0;

            foreach ($keywords as $kw) {
                if (strlen($kw) < 3) {
                    continue;
                }

                if (str_contains($chunkText, $kw)) {
                    $score += 3;
                }
                if (str_contains($docTitle, $kw)) {
                    $score += 2;
                }
            }

            if ($score > 0) {
                $chunk->relevance_score = $score;
                $scoredChunks->push($chunk);
            }
        }

        return $scoredChunks->sortByDesc('relevance_score')->take($limit)->values();
    }
}
