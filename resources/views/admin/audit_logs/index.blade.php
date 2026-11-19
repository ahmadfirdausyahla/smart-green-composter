<x-admin-layout title="Audit Log Sistem" headerTitle="Audit Log & Rekam Jejak Sistem" headerSubtitle="Pencatatan riwayat setiap tindakan administratif, kontrol mesin, dan perubahan konfigurasi">

    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Rekam Jejak Aktivitas (Audit Trail)</h2>
                <p class="text-xs text-slate-500">Log permanen yang mencatat identitas pengguna, IP address, waktu, dan rincian aksi</p>
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
                        <th class="pb-3 font-semibold">Pengguna</th>
                        <th class="pb-3 font-semibold">Tindakan</th>
                        <th class="pb-3 font-semibold">Deskripsi Aktivitas</th>
                        <th class="pb-3 font-semibold text-right">Alamat IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 font-mono-numeric text-slate-500 text-[11px]">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="py-3">
                                <div class="font-bold text-slate-900">{{ $log->user->name ?? 'Sistem / Anonim' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $log->user->role?->label() ?? 'System' }}</div>
                            </td>
                            <td class="py-3">
                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-800">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="py-3 text-slate-700 leading-relaxed max-w-md">
                                {{ $log->description }}
                            </td>
                            <td class="py-3 text-right font-mono-numeric text-slate-500 text-[11px]">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">Belum ada catatan aktivitas.</td>
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
