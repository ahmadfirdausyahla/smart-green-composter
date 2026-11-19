<x-admin-layout title="Persetujuan Operasi Mesin" headerTitle="Manajemen Persetujuan Pengoperasian Mesin" headerSubtitle="Otorisasi izin kendali jarak jauh dan operasional mesin dari Petugas Lapangan atau Admin Kota">

    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Daftar Permohonan Izin Operasi</h2>
                <p class="text-xs text-slate-500">Izin yang disetujui akan mengaktifkan tombol kendali remote untuk durasi tertentu</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200">
                Wewenang: {{ auth()->user()->role->label() }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200/80 text-slate-500 uppercase tracking-wider text-[10px]">
                        <th class="pb-3 font-semibold">Pemohon</th>
                        <th class="pb-3 font-semibold">Perangkat & Wilayah</th>
                        <th class="pb-3 font-semibold">Tindakan</th>
                        <th class="pb-3 font-semibold">Durasi Izin</th>
                        <th class="pb-3 font-semibold">Waktu Pengajuan</th>
                        <th class="pb-3 font-semibold text-center">Status</th>
                        <th class="pb-3 font-semibold text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($permissions as $perm)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5">
                                <div class="font-bold text-slate-900">{{ $perm->requester->name }}</div>
                                <div class="text-[11px] text-slate-500">{{ $perm->requester->role->label() }}</div>
                            </td>
                            <td class="py-3.5">
                                <div class="font-bold text-slate-900">{{ $perm->device->device_code }}</div>
                                <div class="text-[11px] text-slate-500">RT {{ $perm->device->neighborhood->rt_number ?? '-' }} / RW {{ $perm->device->neighborhood->rw_number ?? '-' }}</div>
                            </td>
                            <td class="py-3.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                                    {{ $perm->action_type }}
                                </span>
                            </td>
                            <td class="py-3.5 font-mono-numeric font-medium text-slate-700">
                                {{ $perm->duration_minutes_allowed }} Menit
                            </td>
                            <td class="py-3.5 text-slate-500 font-mono-numeric">
                                {{ $perm->requested_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3.5 text-center">
                                @if ($perm->status === 'approved')
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        DISETUJUI (Hingga {{ $perm->expires_at ? $perm->expires_at->format('H:i') : '' }})
                                    </span>
                                @elseif ($perm->status === 'rejected')
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                        DITOLAK
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200 animate-pulse">
                                        MENUNGGU
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 text-right">
                                @if ($perm->status === 'pending')
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Approve Button Form -->
                                        <form method="POST" action="{{ route('admin.approvals.respond', $perm->id) }}">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-[11px] shadow-xs transition">
                                                Setujui
                                            </button>
                                        </form>

                                        <!-- Reject Button Form -->
                                        <form method="POST" action="{{ route('admin.approvals.respond', $perm->id) }}">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-semibold text-[11px] transition">
                                                Tolak
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-[11px] text-slate-400">Selesai direspons</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Tidak ada pengajuan izin pengoperasian mesin saat ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $permissions->links() }}
        </div>
    </div>

</x-admin-layout>
