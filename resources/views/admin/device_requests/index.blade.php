<x-admin-layout title="Pengajuan Unit Alat" headerTitle="Pengajuan Penambahan Unit Alat" headerSubtitle="Permohonan stok alat dari Kota ke Pusat dan RT/RW ke Kota">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        @if (auth()->user()->isAdminRtRw() || auth()->user()->isAdminKota())
            <!-- Form Pengajuan Unit Baru (Admin RT/RW atau Admin Kota) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <h3 class="text-sm font-bold text-slate-900 mb-1">Form Pengajuan Unit Alat</h3>
                <p class="text-xs text-slate-500 mb-4">
                    {{ auth()->user()->isAdminKota() ? 'Ajukan tambahan stok alat kota ke Master Admin' : 'Ajukan unit komposter tambahan ke Admin Kota' }}
                </p>

                <form method="POST" action="{{ route('admin.device-requests.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Pemohon</label>
                        <div class="mt-1 p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800">
                            {{ auth()->user()->isAdminKota() ? (auth()->user()->city->name ?? 'Kota Anda') : (auth()->user()->neighborhood->full_label ?? 'RT/RW Anda') }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Jumlah Alat Diminta</label>
                        <input type="number" name="requested_count" min="1" max="{{ auth()->user()->isAdminKota() ? 100 : 20 }}" value="1" required
                            class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 font-mono-numeric focus:outline-none focus:border-emerald-600">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Alasan Kebutuhan</label>
                        <textarea name="reason" rows="3" required placeholder="Jelaskan kebutuhan penambahan unit..."
                            class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-emerald-600"></textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-xs transition">
                        {{ auth()->user()->isAdminKota() ? 'Kirim Pengajuan ke Master Admin' : 'Kirim Pengajuan ke Admin Kota' }}
                    </button>
                </form>
            </div>
        @else
            <!-- Penjelasan untuk Master Admin -->
            <div class="bg-slate-50 rounded-2xl border border-slate-200/80 p-5">
                <h3 class="text-sm font-bold text-slate-900 mb-1">Persetujuan & Distribusi Langsung</h3>
                <p class="text-xs text-slate-600 leading-relaxed mt-2">
                    Saat pengajuan disetujui, sistem langsung mengarahkan ke form distribusi dengan pilihan unit acak dari stok yang tersedia.
                </p>
            </div>
        @endif

        <!-- Daftar Pengajuan (Col-Span 2) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
            <h3 class="text-sm font-bold text-slate-900 mb-4">Daftar Pengajuan Unit Alat</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200/80 text-slate-500 uppercase tracking-wider text-[10px]">
                            <th class="pb-3 font-semibold">Pemohon & Wilayah</th>
                            <th class="pb-3 font-semibold">Jumlah</th>
                            <th class="pb-3 font-semibold">Alasan</th>
                            <th class="pb-3 font-semibold text-center">Status</th>
                            @if (! auth()->user()->isAdminRtRw())
                                <th class="pb-3 font-semibold text-right">Tindakan</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($requests as $r)
                            @php
                                $canApprove = (auth()->user()->isMasterAdmin() && $r->neighborhood_id === null)
                                    || (auth()->user()->isAdminKota() && $r->neighborhood_id !== null);
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5">
                                    @if ($r->neighborhood)
                                        <div class="font-bold text-slate-900">RT {{ $r->neighborhood->rt_number }} / RW {{ $r->neighborhood->rw_number }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $r->neighborhood->village->name ?? '' }}, {{ $r->city->name ?? '' }}</div>
                                    @else
                                        <div class="font-bold text-emerald-800">{{ $r->city->name ?? 'Kota' }}</div>
                                        <div class="text-[11px] text-slate-500">Pengajuan Stok Kota</div>
                                    @endif
                                </td>
                                <td class="py-3.5 font-mono-numeric font-bold text-slate-900">
                                    {{ $r->requested_count }} Unit
                                </td>
                                <td class="py-3.5 text-slate-600 max-w-xs truncate" title="{{ $r->reason }}">
                                    {{ $r->reason }}
                                    @if ($r->review_notes)
                                        <div class="text-[10px] text-emerald-700 truncate">{{ $r->review_notes }}</div>
                                    @endif
                                </td>
                                <td class="py-3.5 text-center">
                                    @php
                                        $badgeColor = match($r->status) {
                                            'completed' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                            'approved', 'processing' => 'bg-blue-50 text-blue-800 border-blue-200',
                                            'rejected' => 'bg-rose-50 text-rose-800 border-rose-200',
                                            default => 'bg-amber-50 text-amber-800 border-amber-200',
                                        };
                                    @endphp
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold border {{ $badgeColor }}">
                                        {{ strtoupper($r->status) }}
                                    </span>
                                </td>
                                @if (! auth()->user()->isAdminRtRw())
                                    <td class="py-3.5 text-right">
                                        @if ($r->status === 'pending' && $canApprove)
                                            <div class="flex items-center justify-end gap-1.5">
                                                <form method="POST" action="{{ route('admin.device-requests.update-status', $r->id) }}">
                                                    @csrf
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="px-2.5 py-1 rounded bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-[10px] transition">
                                                        Setujui & Distribusi
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.device-requests.update-status', $r->id) }}">
                                                    @csrf
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="px-2 py-1 rounded bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-semibold text-[10px] transition">
                                                        Tolak
                                                    </button>
                                                </form>
                                            </div>
                                        @elseif ($r->status === 'approved' && $canApprove)
                                            <a href="{{ route('admin.inventory.index', ['request_id' => $r->id]) }}"
                                                class="inline-flex px-2.5 py-1 rounded bg-blue-600 text-white font-semibold text-[10px] hover:bg-blue-700 transition">
                                                Kirim Sekarang &rarr;
                                            </a>
                                        @else
                                            <span class="text-[10px] text-slate-400">{{ $r->status === 'completed' ? 'Terkirim' : 'Menunggu' }}</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">Belum ada pengajuan unit alat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $requests->links() }}
            </div>
        </div>
    </div>

</x-admin-layout>
