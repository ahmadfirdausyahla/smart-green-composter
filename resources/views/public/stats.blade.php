<x-public-layout title="Statistik Dampak & Reduksi Massa — Smart Green Composter">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Statistik Dampak Pengolahan Sampah Organik</h1>
            <p class="text-xs text-slate-500 mt-1">Transparansi data reduksi massa dan output kompos di seluruh unit Smart Green Composter</p>
        </div>

        <!-- 4 Key Impact Numbers -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Input Sampah Organik</span>
                <div class="text-3xl font-extrabold text-slate-900 font-mono-numeric mt-2">
                    {{ number_format($totalInitialWeight, 1) }}<span class="text-sm font-normal text-slate-400 ml-1">Kg</span>
                </div>
                <div class="text-[11px] text-slate-500 mt-2">Total sampah organik dimasukkan</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Hasil Kompos Akhir</span>
                <div class="text-3xl font-extrabold text-slate-900 font-mono-numeric mt-2">
                    {{ number_format($totalFinalWeight, 1) }}<span class="text-sm font-normal text-slate-400 ml-1">Kg</span>
                </div>
                <div class="text-[11px] text-slate-500 mt-2">Pupuk organik terdekomposisi</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Reduksi Massa Kompos</span>
                <div class="text-3xl font-extrabold text-emerald-700 font-mono-numeric mt-2">
                    {{ number_format($totalReductionKg, 1) }}<span class="text-sm font-normal text-slate-400 ml-1">Kg</span>
                </div>
                <div class="text-[11px] text-slate-500 mt-2">Penyusutan massa melalui pencacahan & dehidrasi</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Rata-rata Reduksi</span>
                <div class="text-3xl font-extrabold text-emerald-700 font-mono-numeric mt-2">
                    {{ $avgReductionPct }}<span class="text-sm font-normal text-slate-400 ml-1">%</span>
                </div>
                <div class="text-[11px] text-slate-500 mt-2">Dari {{ $totalSessions }} siklus pengolahan sukses</div>
            </div>
        </div>

        <!-- Scientific Methodology Explanation -->
        <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-6 mb-8">
            <h2 class="text-sm font-bold text-slate-900 mb-2">Metodologi Perhitungan Reduksi Massa</h2>
            <p class="text-xs text-slate-600 leading-relaxed max-w-3xl mb-3">
                Smart Green Composter menggunakan sensor LoadCell HX711 terkalibrasi untuk menimbang berat sampah sebelum dan sesudah siklus pemrosesan 4 jam. Reduksi massa terjadi akibat penguapan kadar air (moisture evaporation) dan pencacahan mekanis menjadi partikel kompos mikro:
            </p>
            <div class="inline-block p-3 rounded-xl bg-white border border-slate-200 text-xs font-mono-numeric text-slate-800">
                Reduksi Massa (%) = ((Berat Awal - Berat Akhir) / Berat Awal) × 100%
            </div>
        </div>

        <!-- Recent Completed Cycles Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
            <h2 class="text-sm font-bold text-slate-900 mb-1">Riwayat Siklus Pengolahan Terakhir</h2>
            <p class="text-xs text-slate-500 mb-4">Data riil hasil pembacaan sensor setiap unit mesin</p>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200/80 text-slate-500 uppercase tracking-wider text-[10px]">
                            <th class="pb-3 font-semibold">Unit & Wilayah</th>
                            <th class="pb-3 font-semibold">Waktu Mulai</th>
                            <th class="pb-3 font-semibold text-right">Berat Awal</th>
                            <th class="pb-3 font-semibold text-right">Berat Akhir</th>
                            <th class="pb-3 font-semibold text-right">Selisih Reduksi</th>
                            <th class="pb-3 font-semibold text-right">Efisiensi %</th>
                            <th class="pb-3 font-semibold text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($recentSessions as $s)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3">
                                    <div class="font-bold text-slate-900">{{ $s->device->device_code }}</div>
                                    <div class="text-[11px] text-slate-500">RT {{ $s->device->neighborhood->rt_number ?? '-' }} / RW {{ $s->device->neighborhood->rw_number ?? '-' }}</div>
                                </td>
                                <td class="py-3 text-slate-600 font-mono-numeric">
                                    {{ $s->started_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-3 text-right font-mono-numeric font-semibold text-slate-800">
                                    {{ number_format($s->initial_weight, 1) }} Kg
                                </td>
                                <td class="py-3 text-right font-mono-numeric font-semibold text-slate-800">
                                    {{ number_format($s->final_weight, 1) }} Kg
                                </td>
                                <td class="py-3 text-right font-mono-numeric font-bold text-emerald-700">
                                    {{ number_format($s->weight_reduction_kg, 1) }} Kg
                                </td>
                                <td class="py-3 text-right font-mono-numeric font-bold text-emerald-700">
                                    {{ $s->reduction_percentage }}%
                                </td>
                                <td class="py-3 text-center">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        SELESAI
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">Belum ada riwayat siklus yang tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-public-layout>
