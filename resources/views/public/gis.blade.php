<x-public-layout title="Peta Persebaran GIS — Smart Green Composter">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Peta Persebaran Geografis Mesin Komposter</h1>
                <p class="text-xs text-slate-500">Menampilkan titik lokasi perangkat aktif di berbagai RT/RW dan Kota di Indonesia</p>
            </div>

            <!-- Legend Badge -->
            <div class="flex items-center gap-2 text-xs">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span> Siap / Normal
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 text-blue-800 border border-blue-200 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span> Sedang Beroperasi
                </span>
            </div>
        </div>

        <!-- Full Map Container -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-3 shadow-xs mb-8">
            <div id="fullGisMap" class="w-full h-[520px] rounded-xl border border-slate-200/60 overflow-hidden z-10"></div>
        </div>

        <!-- Devices Directory Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
            <h2 class="text-sm font-bold text-slate-900 mb-1">Daftar Penempatan Perangkat Terdaftar</h2>
            <p class="text-xs text-slate-500 mb-4">Informasi publik unit Smart Green Composter</p>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200/80 text-slate-500 uppercase tracking-wider text-[10px]">
                            <th class="pb-3 font-semibold">Kode Perangkat</th>
                            <th class="pb-3 font-semibold">Model Spesifikasi</th>
                            <th class="pb-3 font-semibold">Provinsi & Kota</th>
                            <th class="pb-3 font-semibold">Wilayah RT / RW</th>
                            <th class="pb-3 font-semibold text-center">Status Operasional</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($devices as $d)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3 font-bold font-mono-numeric text-slate-900">{{ $d['code'] }}</td>
                                <td class="py-3 text-slate-700">{{ $d['model'] }}</td>
                                <td class="py-3 text-slate-700">{{ $d['city'] }}, {{ $d['province'] }}</td>
                                <td class="py-3 text-slate-600">{{ $d['location'] }}</td>
                                <td class="py-3 text-center">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold {{ $d['status'] === 'running' ? 'bg-blue-50 text-blue-800 border border-blue-200' : 'bg-emerald-50 text-emerald-800 border border-emerald-200' }}">
                                        {{ $d['status_label'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">Belum ada unit yang terdaftar untuk publik.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const devices = @json($devices);
            const defaultLat = devices.length > 0 ? devices[0].lat : -6.597147;
            const defaultLng = devices.length > 0 ? devices[0].lng : 106.806039;

            const map = L.map('fullGisMap').setView([defaultLat, defaultLng], 11);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            devices.forEach(function (d) {
                const color = d.status === 'running' ? '#2563eb' : '#059669';
                const customIcon = L.divIcon({
                    className: 'custom-pin',
                    html: `<div style="background-color: ${color}; width: 14px; height: 14px; border-radius: 50%; border: 2.5px solid #ffffff; box-shadow: 0 1px 4px rgba(0,0,0,0.3);"></div>`,
                    iconSize: [14, 14],
                    iconAnchor: [7, 7]
                });

                const marker = L.marker([d.lat, d.lng], { icon: customIcon }).addTo(map);
                marker.bindPopup(`
                    <div style="font-family: inherit; font-size: 12px; min-width: 180px;">
                        <strong style="color: #0f172a; font-size: 13px;">${d.code}</strong><br>
                        <span style="color: #64748b; font-size: 11px;">${d.model}</span>
                        <div style="margin-top: 6px; padding-top: 6px; border-top: 1px solid #e2e8f0;">
                            <div>Status: <strong>${d.status_label}</strong></div>
                            <div style="color: #64748b; font-size: 10px; margin-top: 4px;">${d.location}</div>
                        </div>
                    </div>
                `);
            });
        });
    </script>
</x-public-layout>
