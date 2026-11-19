<x-admin-layout title="Dashboard Wilayah" headerTitle="Dashboard Statistik Wilayah" headerSubtitle="Ringkasan pemantauan pengolahan sampah dan sebaran mesin komposter">

    <!-- TOP STATISTIC CARDS (White-Green Clean) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- 1. Total Unit Komposter -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Perangkat</span>
                <span class="p-2 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl font-bold text-slate-900 font-mono-numeric">{{ $totalDevices }}</span>
                <span class="text-xs font-medium text-slate-500">Unit Terpasang</span>
            </div>
            <div class="mt-3 flex items-center gap-3 text-[11px] text-slate-500 border-t border-slate-100 pt-2.5">
                <span class="text-emerald-700 font-semibold">{{ $runningDevices }} Beroperasi</span>
                <span>•</span>
                <span class="text-slate-600">{{ $activeDevices - $runningDevices }} Standby</span>
                <span>•</span>
                <span class="text-rose-600 font-semibold">{{ $offlineDevices }} Offline</span>
            </div>
        </div>

        <!-- 2. Sampah Organik Diproses (Input) -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Sampah Diproses</span>
                <span class="p-2 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                    </svg>
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl font-bold text-slate-900 font-mono-numeric">{{ number_format($totalInitialWeight, 1) }}</span>
                <span class="text-xs font-medium text-slate-500">Kg Organik</span>
            </div>
            <div class="mt-3 text-[11px] text-slate-500 border-t border-slate-100 pt-2.5">
                Hasil kompos akhir: <strong class="text-slate-800 font-mono-numeric">{{ number_format($totalFinalWeight, 1) }} Kg</strong>
            </div>
        </div>

        <!-- 3. Reduksi Massa Sampah -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Reduksi Massa</span>
                <span class="p-2 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl font-bold text-emerald-700 font-mono-numeric">{{ $avgReductionPct }}%</span>
                <span class="text-xs font-medium text-slate-500">Efisiensi Rata-rata</span>
            </div>
            <div class="mt-3 text-[11px] text-slate-500 border-t border-slate-100 pt-2.5">
                Total pengurangan: <strong class="text-emerald-800 font-mono-numeric">{{ number_format($totalReductionKg, 1) }} Kg</strong>
            </div>
        </div>

        <!-- 4. Laporan Kendala & Kesehatan Alat -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tiket Gangguan</span>
                <span class="p-2 rounded-xl {{ $openReportsCount > 0 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-50 text-slate-600 border border-slate-200' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl font-bold {{ $openReportsCount > 0 ? 'text-amber-700' : 'text-slate-900' }} font-mono-numeric">{{ $openReportsCount }}</span>
                <span class="text-xs font-medium text-slate-500">Menunggu Penanganan</span>
            </div>
            <div class="mt-3 text-[11px] text-slate-500 border-t border-slate-100 pt-2.5">
                Kondisi alat: <strong class="{{ $faultyHealthDevices > 0 ? 'text-amber-700' : 'text-emerald-700' }}">{{ $faultyHealthDevices > 0 ? $faultyHealthDevices . ' Butuh Servis' : '100% Normal' }}</strong>
            </div>
        </div>
    </div>

    <!-- MAIN GRID: GIS MAP & REDUCTION BREAKDOWN -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- GIS Map Card (Col-Span 2) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Peta Persebaran Mesin Komposter</h2>
                    <p class="text-xs text-slate-500">Titik lokasi unit sesuai yurisdiksi: {{ auth()->user()->jurisdiction_name }}</p>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-[11px]">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Siap / Standby
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-800 border border-blue-200 text-[11px]">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span> Beroperasi
                    </span>
                </div>
            </div>

            <!-- Leaflet Map Container -->
            <div id="gisMap" class="w-full h-80 rounded-xl border border-slate-200/80 overflow-hidden z-10"></div>
        </div>

        <!-- Reduction Metric Card (Col-Span 1) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900 mb-1">Rasio Reduksi Massa Sampah</h2>
                <p class="text-xs text-slate-500 mb-4">Efektivitas proses penggilingan & dekomposisi</p>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 mb-4">
                    <div class="flex items-center justify-between text-xs text-slate-600 mb-1">
                        <span>Total Input Sampah</span>
                        <span class="font-bold text-slate-900 font-mono-numeric">{{ number_format($totalInitialWeight, 1) }} Kg</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-2 mb-3">
                        <div class="bg-emerald-600 h-2 rounded-full" style="width: 100%"></div>
                    </div>

                    <div class="flex items-center justify-between text-xs text-slate-600 mb-1">
                        <span>Hasil Kompos Terkumpul</span>
                        <span class="font-bold text-slate-900 font-mono-numeric">{{ number_format($totalFinalWeight, 1) }} Kg</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-2 mb-3">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $totalInitialWeight > 0 ? ($totalFinalWeight / $totalInitialWeight) * 100 : 0 }}%"></div>
                    </div>

                    <div class="flex items-center justify-between text-xs text-slate-600 mb-1">
                        <span class="font-semibold text-emerald-800">Reduksi Massa (Penguapan & Cacah)</span>
                        <span class="font-bold text-emerald-700 font-mono-numeric">{{ number_format($totalReductionKg, 1) }} Kg ({{ $avgReductionPct }}%)</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-2">
                        <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $avgReductionPct }}%"></div>
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-emerald-50/60 border border-emerald-100 text-[11px] text-emerald-900 leading-relaxed">
                    <strong>Formula Akurasi:</strong> Reduksi massa dihitung otomatis dari selisih timbangan sensor LoadCell HX711 sebelum dan sesudah siklus penggilingan.
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100">
                <a href="{{ route('admin.monitoring.index') }}"
                    class="flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-xl bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700 transition">
                    <span>Buka Panel Monitoring & Remote</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- BOTTOM ROW: RECENT SESSIONS & AUDIT LOG -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Sessions Table (Col-Span 2) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Riwayat Pengolahan Sampah Terakhir</h3>
                    <p class="text-xs text-slate-500">Hasil pencatatan sensor timbangan per siklus proses</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200/80 text-slate-500 uppercase tracking-wider text-[10px]">
                            <th class="pb-3 font-semibold">Perangkat & Lokasi</th>
                            <th class="pb-3 font-semibold">Operator</th>
                            <th class="pb-3 font-semibold text-right">Berat Awal</th>
                            <th class="pb-3 font-semibold text-right">Hasil Akhir</th>
                            <th class="pb-3 font-semibold text-right">Reduksi</th>
                            <th class="pb-3 font-semibold text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($recentSessions as $session)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3">
                                    <div class="font-bold text-slate-900">{{ $session->device->device_code }}</div>
                                    <div class="text-[11px] text-slate-500">RT {{ $session->device->neighborhood->rt_number ?? '-' }} / RW {{ $session->device->neighborhood->rw_number ?? '-' }}</div>
                                </td>
                                <td class="py-3 text-slate-700">
                                    {{ $session->operator->name ?? 'Petugas' }}
                                </td>
                                <td class="py-3 text-right font-mono-numeric font-semibold text-slate-800">
                                    {{ number_format($session->initial_weight, 1) }} Kg
                                </td>
                                <td class="py-3 text-right font-mono-numeric font-semibold text-slate-800">
                                    {{ $session->final_weight ? number_format($session->final_weight, 1) . ' Kg' : '-' }}
                                </td>
                                <td class="py-3 text-right font-mono-numeric font-bold text-emerald-700">
                                    {{ $session->weight_reduction_kg ? number_format($session->weight_reduction_kg, 1) . ' Kg (' . $session->reduction_percentage . '%)' : '-' }}
                                </td>
                                <td class="py-3 text-center">
                                    <span class="inline-flex px-2 py-0.5 text-[10px] font-semibold rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        Selesai
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-slate-400">Belum ada riwayat sesi pengolahan di wilayah ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Activity Audit Log (Col-Span 1) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
            <h3 class="text-sm font-bold text-slate-900 mb-1">Log Aktivitas Sistem</h3>
            <p class="text-xs text-slate-500 mb-4">Audit log terpusat setiap tindakan berwenang</p>

            <div class="space-y-3">
                @forelse ($recentActivities as $act)
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-start gap-2.5">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 mt-1.5 shrink-0"></div>
                        <div class="min-w-0 flex-1">
                            <div class="text-xs font-semibold text-slate-900 leading-tight truncate">{{ $act->action }}</div>
                            <div class="text-[11px] text-slate-600 line-clamp-2 mt-0.5">{{ $act->description }}</div>
                            <div class="text-[10px] text-slate-400 mt-1 font-mono-numeric">{{ $act->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-xs text-slate-400">Belum ada riwayat aktivitas.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Leaflet GIS Map Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mapData = @json($mapPins);
            
            // Center of Indonesia or default location
            const defaultLat = mapData.length > 0 ? mapData[0].lat : -6.597147;
            const defaultLng = mapData.length > 0 ? mapData[0].lng : 106.806039;

            const map = L.map('gisMap', {
                scrollWheelZoom: false
            }).setView([defaultLat, defaultLng], mapData.length > 0 ? 13 : 6);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            mapData.forEach(function (device) {
                const color = device.status === 'running' ? '#2563eb' : (device.status === 'idle' ? '#059669' : '#dc2626');
                
                const customIcon = L.divIcon({
                    className: 'custom-pin',
                    html: `<div style="background-color: ${color}; width: 14px; height: 14px; border-radius: 50%; border: 2.5px solid #ffffff; box-shadow: 0 1px 4px rgba(0,0,0,0.3);"></div>`,
                    iconSize: [14, 14],
                    iconAnchor: [7, 7]
                });

                const marker = L.marker([device.lat, device.lng], { icon: customIcon }).addTo(map);

                marker.bindPopup(`
                    <div style="font-family: inherit; font-size: 12px; min-width: 170px;">
                        <strong style="color: #0f172a; font-size: 13px;">${device.code}</strong><br>
                        <span style="color: #64748b; font-size: 11px;">${device.model}</span>
                        <div style="margin-top: 6px; padding-top: 6px; border-top: 1px solid #e2e8f0;">
                            <div>Status: <strong>${device.status_label}</strong></div>
                            <div>Suhu: <strong>${device.temperature} °C</strong></div>
                            <div>Berat: <strong>${device.weight} Kg</strong></div>
                            <div style="color: #64748b; font-size: 10px; margin-top: 4px;">${device.location}</div>
                        </div>
                    </div>
                `);
            });
        });
    </script>
</x-admin-layout>
