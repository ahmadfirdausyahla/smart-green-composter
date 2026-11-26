<x-admin-layout title="Audit Log Sistem" headerTitle="Audit Log & Rekam Jejak Sistem" headerSubtitle="Pencatatan riwayat setiap tindakan administratif, kontrol mesin, dan integritas rantai hash tamper-evident">

    <!-- Integrity Banner -->
    <div class="mb-5 bg-white rounded-2xl border {{ ($integrity['valid'] ?? true) ? 'border-emerald-200 bg-emerald-50/30' : 'border-rose-200 bg-rose-50/30' }} p-4 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl {{ ($integrity['valid'] ?? true) ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white' }} flex items-center justify-center shrink-0">
                    @if ($integrity['valid'] ?? true)
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    @endif
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs font-bold text-slate-900">Status Integritas Kriptografis (Tamper-Evident SHA-256)</h3>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ ($integrity['valid'] ?? true) ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ ($integrity['valid'] ?? true) ? 'TERVERIFIKASI AMAN' : 'PERINGATAN INTEGRITAS' }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-600 mt-0.5">{{ $integrity['message'] ?? 'Memeriksa keutuhan rantai hash...' }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.audit-logs.verify') }}" class="shrink-0">
                @csrf
                <button type="submit" class="w-full sm:w-auto px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-xs transition flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Uji Integritas Rantai Hash</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Logs Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Rekam Jejak Aktivitas Append-Only</h2>
                <p class="text-xs text-slate-500">Log permanen yang mencatat identitas pelaku, peran, perintah IoT, alamat IP, dan bukti hash rantai (SHA-256)</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200">
                Total Log: {{ $logs->total() }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200/80 text-slate-500 uppercase tracking-wider text-[10px]">
                        <th class="pb-3 font-semibold">Waktu (WIB)</th>
                        <th class="pb-3 font-semibold">Pengguna & Peran</th>
                        <th class="pb-3 font-semibold">Kategori / Aksi</th>
                        <th class="pb-3 font-semibold">Deskripsi Aktivitas</th>
                        <th class="pb-3 font-semibold">Bukti Hash SHA-256</th>
                        <th class="pb-3 font-semibold text-right">Alamat IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 font-mono-numeric text-slate-500 text-[11px] whitespace-nowrap">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="py-3 whitespace-nowrap">
                                <div class="font-bold text-slate-900">{{ $log->user->name ?? 'Sistem / Anonim' }}</div>
                                <div class="text-[10px] font-semibold text-emerald-700">{{ $log->actor_role ?? $log->user->role?->label() ?? 'System' }}</div>
                            </td>
                            <td class="py-3 whitespace-nowrap">
                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-800">
                                    {{ $log->action }}
                                </span>
                                @if ($log->category && $log->category !== 'SYSTEM')
                                    <span class="ml-1 text-[9px] font-semibold text-slate-400">[{{ $log->category }}]</span>
                                @endif
                            </td>
                            <td class="py-3 text-slate-700 leading-relaxed max-w-sm">
                                {{ $log->description }}
                            </td>
                            <td class="py-3 font-mono-numeric text-[10px] text-slate-500">
                                @if ($log->current_hash)
                                    <div class="flex items-center gap-1.5" title="SHA-256: {{ $log->current_hash }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span class="font-semibold text-slate-800">{{ substr($log->current_hash, 0, 8) }}...{{ substr($log->current_hash, -6) }}</span>
                                    </div>
                                    <div class="text-[9px] text-slate-400" title="Prev: {{ $log->previous_hash }}">
                                        &larr; {{ substr($log->previous_hash ?? '', 0, 6) }}...
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">Legacy Genesis</span>
                                @endif
                            </td>
                            <td class="py-3 text-right font-mono-numeric text-slate-500 text-[11px] whitespace-nowrap">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Belum ada catatan aktivitas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>

</x-admin-layout>
