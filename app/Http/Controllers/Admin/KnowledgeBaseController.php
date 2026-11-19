<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\KnowledgeChunk;
use App\Models\KnowledgeDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KnowledgeBaseController extends Controller
{
    public function index(Request $request): View
    {
        $documents = KnowledgeDocument::withCount('chunks')->latest()->paginate(10);
        return view('admin.knowledge_base.index', compact('documents'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isMasterAdmin() && ! $user->isAdminKota()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:50'],
            'content' => ['required', 'string', 'min:20'],
        ]);

        $document = KnowledgeDocument::create([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'source_type' => 'manual',
            'content' => $validated['content'],
            'is_published' => true,
        ]);

        // Auto Chunking: Split paragraphs into manageable chunks for RAG matching
        $paragraphs = array_filter(array_map('trim', explode("\n", $validated['content'])));
        $order = 1;
        foreach ($paragraphs as $para) {
            if (strlen($para) > 15) {
                KnowledgeChunk::create([
                    'document_id' => $document->id,
                    'chunk_text' => $para,
                    'chunk_order' => $order++,
                ]);
            }
        }

        AuditLog::record(
            action: 'KNOWLEDGE_DOC_CREATED',
            description: "Dokumen pengetahuan '{$document->title}' ditambahkan oleh {$user->name} dan dibagi menjadi " . ($order - 1) . " chunk.",
            entityType: 'KnowledgeDocument',
            entityId: $document->id
        );

        return back()->with('success', "Dokumen pengetahuan berhasil ditambahkan dan diindeks untuk AI Chatbot ({$order} segmen terindeks).");
    }

    public function destroy(Request $request, KnowledgeDocument $knowledgeDocument): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isMasterAdmin() && ! $user->isAdminKota()) {
            abort(403);
        }

        $title = $knowledgeDocument->title;
        $knowledgeDocument->delete();

        AuditLog::record(
            action: 'KNOWLEDGE_DOC_DELETED',
            description: "Dokumen pengetahuan '{$title}' dihapus oleh {$user->name}."
        );

        return back()->with('success', "Dokumen '{$title}' berhasil dihapus dari basis pengetahuan AI.");
    }
}
