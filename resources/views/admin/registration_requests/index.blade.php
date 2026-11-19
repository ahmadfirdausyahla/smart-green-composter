<x-admin-layout title="Verifikasi Pendaftaran RT/RW" headerTitle="Verifikasi Pendaftaran Admin RT/RW" headerSubtitle="Peninjauan data pengurus lingkungan dan aktivasi akun RT/RW baru">

    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Daftar Pengajuan Akun RT/RW</h2>
                <p class="text-xs text-slate-500">Persetujuan akan otomatis membuat akun Admin RT/RW aktif dan entitas lingkungan baru</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200">
                Yurisdiksi: {{ auth()->user()->jurisdiction_name }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200/80 text-slate-500 uppercase tracking-wider text-[10px]">
                        <th class="pb-3 font-semibold">Nama Pemohon</th>
                        <th class="pb-3 font-semibold">Kontak (Email / HP)</th>
                        <th class="pb-3 font-semibold">Wilayah RT / RW</th>
                        <th class="pb-3 font-semibold">Kelurahan / Kecamatan</th>
                        <th class="pb-3 font-semibold text-center">Status</th>
                        <th class="pb-3 font-semibold text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($requests as $req)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5">
                                <div class="font-bold text-slate-900">{{ $req->name }}</div>
                                <div class="text-[11px] text-slate-500">{{ $req->created_at->translatedFormat('d M Y, H:i') }}</div>
                            </td>
                            <td class="py-3.5">
                                <div class="text-slate-800">{{ $req->email }}</div>
                                <div class="text-[11px] text-slate-500 font-mono-numeric">{{ $req->phone_number }}</div>
                            </td>
                            <td class="py-3.5">
                                <span class="font-bold text-slate-900">RT {{ $req->rt_number }} / RW {{ $req->rw_number }}</span>
                                <div class="text-[11px] text-slate-500 truncate max-w-xs">{{ $req->supporting_info ?? 'Tanpa keterangan tambahan' }}</div>
                            </td>
                            <td class="py-3.5 text-slate-700">
                                <div>{{ $req->village->name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $req->district->name ?? '-' }}, {{ $req->city->name ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 text-center">
                                @if ($req->status === 'approved')
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        DISETUJUI
                                    </span>
                                @elseif ($req->status === 'rejected')
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                        DITOLAK
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200 animate-pulse">
                                        MENUNGGU VERIFIKASI
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 text-right">
                                @if ($req->status === 'pending')
                                    <div class="flex items-center justify-end gap-2">
                                        <form method="POST" action="{{ route('admin.registration-requests.approve', $req->id) }}"
                                            onsubmit="return confirm('Setujui pendaftaran ini dan aktifkan akun Admin RT/RW?');">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-[11px] shadow-xs transition">
                                                Setujui
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.registration-requests.reject', $req->id) }}"
                                            onsubmit="const reason = prompt('Masukkan alasan penolakan:'); if(!reason) return false; this.querySelector('input[name=review_notes]').value = reason;">
                                            @csrf
                                            <input type="hidden" name="review_notes" value="">
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-semibold text-[11px] transition">
                                                Tolak
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-[11px] text-slate-400">Telah Diverifikasi</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Tidak ada pengajuan pendaftaran baru saat ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $requests->links() }}
        </div>
    </div>

</x-admin-layout>
