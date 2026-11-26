<x-admin-layout title="Laporan & Analitik Pengolahan" headerTitle="Laporan & Rekapitulasi Reduksi Massa" headerSubtitle="Statistik pengolahan sampah organik dan kalkulasi reduksi massa selama proses pengomposan">

    <!-- Top Summary Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
            <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                <span>Total Sampah Diolah</span>
                <span class="w-6 h-6 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-[10px]">IN</span>
            </div>
            <div class="text-2xl font-bold font-mono-numeric text-slate-900 leading-tight">
                {{ number_format($totalInitialWeight, 1) }} <span class="text-xs text-slate-400 font-sans">Kg</span>
            </div>
            <div class="text-[10px] text-slate-400 mt-1">Bobot awal sampah basah</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
            <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                <span>Hasil Akhir Kompos</span>
                <span class="w-6 h-6 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-[10px]">OUT</span>
            </div>
            <div class="text-2xl font-bold font-mono-numeric text-slate-900 leading-tight">
                {{ number_format($totalFinalWeight, 1) }} <span class="text-xs text-slate-400 font-sans">Kg</span>
            </div>
            <div class="text-[10px] text-slate-400 mt-1">Bobot kompos kering matang</div>
        </div>

        <div class="bg-white rounded-2xl border border-emerald-200 p-5 shadow-xs bg-emerald-50/20">
            <div class="flex items-center justify-between text-xs text-emerald-800 font-semibold mb-2">
                <span>Reduksi Massa Pengolahan</span>
                <span class="px-1.5 py-0.5 rounded text-[10px] bg-emerald-100 text-emerald-800 font-bold font-mono-numeric">
                    {{ $reductionPercentage }}%
                </span>
            </div>
            <div class="text-2xl font-bold font-mono-numeric text-emerald-700 leading-tight">
                {{ number_format($totalReductionKg, 1) }} <span class="text-xs text-emerald-600 font-sans">Kg</span>
            </div>
            <div class="text-[10px] text-emerald-700/80 mt-1">Selisih massa selama proses</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
            <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                <span>Total Siklus Selesai</span>
                <span class="w-6 h-6 rounded-lg bg-purple-50 text-purple-700 flex items-center justify-center font-bold text-[10px]">SES</span>
            </div>
            <div class="text-2xl font-bold font-mono-numeric text-slate-900 leading-tight">
                {{ $totalSessions }} <span class="text-xs text-slate-400 font-sans">Sesi</span>
            </div>
            <div class="text-[10px] text-slate-400 mt-1">Rata-rata: {{ $avgDuration }} Menit / sesi</div>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs mb-6">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end text-xs">
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:border-emerald-600">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:border-emerald-600">
            </div>

            @if ($user->isMasterAdmin() && $cities->isNotEmpty())
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kota / Kabupaten</label>
                    <select name="city_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:border-emerald-600">
                        <option value="">Semua Kota</option>
                        @foreach ($cities as $c)
                            <option value="{{ $c->id }}" {{ (string) $cityId === (string) $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if (($user->isMasterAdmin() || $user->isAdminKota()) && $neighborhoods->isNotEmpty())
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Lingkungan RT/RW</label>
                    <select name="neighborhood_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:border-emerald-600">
                        <option value="">Semua RT/RW</option>
                        @foreach ($neighborhoods as $n)
                            <option value="{{ $n->id }}" {{ (string) $neighborhoodId === (string) $n->id ? 'selected' : '' }}>
                                RT {{ $n->rt_number }} / RW {{ $n->rw_number }} ({{ $n->village->name ?? '' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold transition text-center shadow-xs">
                    Terapkan Filter
                </button>
                <a href="{{ route('admin.reports.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Sessions Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Riwayat Siklus Pengolahan</h3>
                <p class="text-xs text-slate-500">Kalkulasi: Reduksi Massa (%) = ((Bobot Awal - Bobot Akhir) / Bobot Awal) &times; 100</p>
            </div>

            <!-- Export to CSV Button -->
            <a href="{{ route('admin.reports.export-csv', request()->query()) }}"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition shadow-xs">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Unduh CSV</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200/80 text-slate-500 uppercase tracking-wider text-[10px]">
                        <th class="pb-3 font-semibold">Perangkat & Wilayah</th>
                        <th class="pb-3 font-semibold">Operator</th>
                        <th class="pb-3 font-semibold">Waktu Mulai - Selesai</th>
                        <th class="pb-3 font-semibold text-right">Bobot Awal</th>
                        <th class="pb-3 font-semibold text-right">Bobot Akhir</th>
                        <th class="pb-3 font-semibold text-right">Reduksi Massa Selama Pengolahan</th>
                        <th class="pb-3 font-semibold text-right">Durasi</th>
                        <th class="pb-3 font-semibold text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($sessions as $s)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3">
                                <span class="font-bold text-slate-900 font-mono-numeric block">{{ $s->device->device_code ?? '-' }}</span>
                                <span class="text-[10px] text-slate-500">
                                    RT {{ $s->device->neighborhood->rt_number ?? '-' }} / RW {{ $s->device->neighborhood->rw_number ?? '-' }}, {{ $s->device->city->name ?? '' }}
                                </span>
                            </td>
                            <td class="py-3 font-semibold text-slate-700">
                                {{ $s->operator->name ?? 'Petugas Lapangan' }}
                            </td>
                            <td class="py-3 font-mono-numeric text-[11px] text-slate-500 whitespace-nowrap">
                                <div>{{ $s->started_at ? $s->started_at->format('d/m/Y H:i') : '-' }}</div>
                                <div class="text-[10px] text-slate-400">&rarr; {{ $s->ended_at ? $s->ended_at->format('H:i') : '-' }}</div>
                            </td>
                            <td class="py-3 text-right font-mono-numeric text-slate-900 font-semibold">
                                {{ number_format($s->initial_weight, 2) }} Kg
                            </td>
                            <td class="py-3 text-right font-mono-numeric text-slate-900 font-semibold">
                                {{ number_format($s->final_weight, 2) }} Kg
                            </td>
                            <td class="py-3 text-right font-mono-numeric">
                                <span class="font-bold text-emerald-700 block">
                                    -{{ number_format($s->weight_reduction_kg, 2) }} Kg
                                </span>
                                <span class="inline-flex px-1.5 py-0.2 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    {{ number_format($s->reduction_percentage, 1) }}%
                                </span>
                            </td>
                            <td class="py-3 text-right font-mono-numeric text-slate-600 text-[11px]">
                                {{ $s->actual_duration_minutes ?? $s->target_duration_minutes ?? 240 }} mnt
                            </td>
                            <td class="py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800">
                                    Selesai
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">Belum ada catatan pengolahan sampah pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $sessions->links() }}
        </div>
    </div>

</x-admin-layout>
