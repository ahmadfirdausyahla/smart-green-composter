<x-admin-layout title="Basis Pengetahuan AI (RAG)" headerTitle="Pengelolaan Basis Pengetahuan AI Chatbot" headerSubtitle="Dokumen resmi, FAQ, dan panduan edukasi untuk sistem RAG asisten masyarakat">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Form Tambah Dokumen Pengetahuan Baru -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
            <h3 class="text-sm font-bold text-slate-900 mb-1">Tambah Dokumen RAG Baru</h3>
            <p class="text-xs text-slate-500 mb-4">Materi akan otomatis di-chunk menjadi segmen teks agar dicocokkan oleh asisten AI</p>

            <form method="POST" action="{{ route('admin.knowledge-base.store') }}" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Judul Dokumen / Topik</label>
                    <input type="text" name="title" required placeholder="Contoh: FAQ Perawatan Tabung Komposter"
                        class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-emerald-600">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Kategori Materi</label>
                    <select name="category" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-emerald-600">
                        <option value="operasional">Operasional Mesin</option>
                        <option value="edukasi">Edukasi Sampah Warga</option>
                        <option value="perawatan">Perawatan & Pembersihan</option>
                        <option value="keselamatan">Prosedur Keselamatan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Konten / Teks Pengetahuan</label>
                    <textarea name="content" rows="6" required placeholder="Tuliskan penjelasan detail, panduan, atau tanya-jawab. Setiap paragraf akan otomatis diindeks sebagai segmen pengetahuan RAG..."
                        class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-emerald-600"></textarea>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-xs transition">
                    Simpan & Indeks ke AI
                </button>
            </form>
        </div>

        <!-- Daftar Dokumen Terindeks (Col-Span 2) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
            <h3 class="text-sm font-bold text-slate-900 mb-1">Daftar Dokumen Pengetahuan Terpublikasi</h3>
            <p class="text-xs text-slate-500 mb-4">Sumber rujukan resmi asisten AI publik Smart Green Composter</p>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200/80 text-slate-500 uppercase tracking-wider text-[10px]">
                            <th class="pb-3 font-semibold">Judul Dokumen</th>
                            <th class="pb-3 font-semibold">Kategori</th>
                            <th class="pb-3 font-semibold text-center">Segmen Chunk</th>
                            <th class="pb-3 font-semibold">Diperbarui</th>
                            <th class="pb-3 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($documents as $doc)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5">
                                    <div class="font-bold text-slate-900">{{ $doc->title }}</div>
                                    <div class="text-[11px] text-slate-500 truncate max-w-sm">{{ Str::limit($doc->content, 70) }}</div>
                                </td>
                                <td class="py-3.5">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        {{ ucfirst($doc->category) }}
                                    </span>
                                </td>
                                <td class="py-3.5 text-center font-mono-numeric font-bold text-slate-800">
                                    {{ $doc->chunks_count }} Chunk
                                </td>
                                <td class="py-3.5 text-slate-500 font-mono-numeric text-[11px]">
                                    {{ $doc->updated_at->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 text-right">
                                    <form method="POST" action="{{ route('admin.knowledge-base.destroy', $doc->id) }}"
                                        onsubmit="return confirm('Hapus dokumen pengetahuan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">Belum ada dokumen yang terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $documents->links() }}
            </div>
        </div>
    </div>

</x-admin-layout>
