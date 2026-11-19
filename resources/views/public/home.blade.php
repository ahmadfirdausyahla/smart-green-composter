<x-public-layout title="Smart Green Composter — Platform Pengolahan Sampah Organik Cerdas IoT">

    <!-- HERO SECTION (White-Green Minimalist) -->
    <section class="relative pt-12 pb-20 overflow-hidden bg-gradient-to-b from-emerald-50/50 via-white to-white border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <!-- Eco Tag -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200/80 text-xs font-bold text-emerald-800 mb-6 shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Inovasi Lingkungan Berbasis IoT & ESP32</span>
                </div>

                <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight leading-[1.15]">
                    Pengolahan Sampah Organik Cerdas di Tingkat <span class="text-emerald-700 underline decoration-emerald-300 decoration-wavy decoration-2">RT & RW</span>
                </h1>

                <p class="mt-5 text-sm sm:text-base text-slate-600 leading-relaxed max-w-2xl mx-auto">
                    Platform kendali dan monitoring mesin komposter otomatis terintegrasi. Menimbang berat sampah secara akurat, menjaga suhu dekomposisi optimal, dan mencatat reduksi massa nyata untuk lingkungan yang lebih bersih.
                </p>

                <!-- Action CTA -->
                <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ route('public.gis') }}"
                        class="px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-xs transition flex items-center gap-2">
                        <span>Lihat Peta Persebaran Mesin</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                    <a href="{{ route('register.rtrw') }}"
                        class="px-5 py-3 rounded-xl bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs sm:text-sm border border-slate-200 shadow-xs transition">
                        Daftarkan Lingkungan RT/RW Anda
                    </a>
                </div>
            </div>

            <!-- LIVE IMPACT COUNTER STRIP -->
            <div class="mt-14 grid grid-cols-2 lg:grid-cols-4 gap-4 max-w-5xl mx-auto">
                <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs text-center">
                    <div class="text-3xl font-extrabold text-slate-900 font-mono-numeric">{{ $totalDevices }}</div>
                    <div class="text-xs font-semibold text-slate-500 mt-1 uppercase tracking-wider">Unit Mesin Aktif</div>
                </div>
                <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs text-center">
                    <div class="text-3xl font-extrabold text-slate-900 font-mono-numeric">{{ $totalCities }}</div>
                    <div class="text-xs font-semibold text-slate-500 mt-1 uppercase tracking-wider">Kota / Kabupaten</div>
                </div>
                <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs text-center">
                    <div class="text-3xl font-extrabold text-slate-900 font-mono-numeric">{{ number_format($totalInitialWeight, 1) }}<span class="text-sm font-normal text-slate-400 ml-1">Kg</span></div>
                    <div class="text-xs font-semibold text-slate-500 mt-1 uppercase tracking-wider">Sampah Diproses</div>
                </div>
                <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs text-center">
                    <div class="text-3xl font-extrabold text-emerald-700 font-mono-numeric">{{ $avgReductionPct }}<span class="text-sm font-normal text-slate-400 ml-1">%</span></div>
                    <div class="text-xs font-semibold text-slate-500 mt-1 uppercase tracking-wider">Reduksi Massa Kompos</div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. CARA KERJA SISTEM -->
    <section id="cara-kerja" class="py-16 bg-white border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="text-xs font-bold text-emerald-700 uppercase tracking-wider mb-2">Metodologi & Alur Proses</h2>
                <h3 class="text-2xl sm:text-3xl font-bold text-slate-900">Bagaimana Smart Green Composter Bekerja?</h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-2">
                    Proses terstandarisasi untuk memastikan sampah organik terurai menjadi kompos bernutrisi dengan efisiensi massa terukur.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Step 1 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white font-bold text-sm flex items-center justify-center mb-4">
                            01
                        </div>
                        <h4 class="text-base font-bold text-slate-900 mb-2">Penimbangan Awal Sampah</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Warga mengumpulkan sampah organik dan memasukkannya ke tabung komposter. Sensor LoadCell HX711 membaca berat awal secara presisi sebelum motor menyala.
                        </p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-200/60 text-[11px] font-semibold text-emerald-700">
                        Otomasi Data Sensor IoT
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white font-bold text-sm flex items-center justify-center mb-4">
                            02
                        </div>
                        <h4 class="text-base font-bold text-slate-900 mb-2">Pencacahan & Pemanasan Dekomposisi</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Setelah izin operasi disetujui, mesin menjalankan timer lokal 4 jam. Motor mencacah sampah sementara elemen pemanas menjaga suhu ideal (40°C - 60°C).
                        </p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-200/60 text-[11px] font-semibold text-emerald-700">
                        Proteksi Overheat Mandiri (Failsafe)
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white font-bold text-sm flex items-center justify-center mb-4">
                            03
                        </div>
                        <h4 class="text-base font-bold text-slate-900 mb-2">Penimbangan Akhir & Reduksi Massa</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Setelah siklus selesai, berat kompos akhir ditimbang. Sistem menghitung persentase reduksi massa (rata-rata 60-70%) dan mencatatnya ke statistik wilayah.
                        </p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-200/60 text-[11px] font-semibold text-emerald-700">
                        Hasil: Kompos Organik Siap Pakai
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. INTERACTIVE GIS MAP SECTION -->
    <section class="py-16 bg-slate-50 border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-8">
                <div>
                    <h2 class="text-xs font-bold text-emerald-700 uppercase tracking-wider mb-1">Persebaran Terintegrasi</h2>
                    <h3 class="text-2xl font-bold text-slate-900">Peta Sebaran Komposter di Indonesia</h3>
                    <p class="text-xs text-slate-500 mt-1">Transparansi lokasi unit komposter aktif di tingkat lingkungan RT/RW</p>
                </div>
                <a href="{{ route('public.gis') }}" class="mt-4 md:mt-0 text-xs font-bold text-emerald-700 hover:text-emerald-800 flex items-center gap-1.5">
                    <span>Buka Layar Penuh GIS</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>

            <div class="bg-white p-3 rounded-2xl border border-slate-200/80 shadow-xs">
                <div id="homeMap" class="w-full h-96 rounded-xl border border-slate-200/60 overflow-hidden z-10"></div>
            </div>
        </div>
    </section>

    <!-- 4. EDUKASI SAMPAH ORGANIK -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="text-xs font-bold text-emerald-700 uppercase tracking-wider mb-2">Panduan Warga</h2>
                <h3 class="text-2xl font-bold text-slate-900">Jenis Sampah yang Layak Olah</h3>
                <p class="text-xs text-slate-500 mt-1">Pastikan hanya sampah organik yang sesuai dimasukkan agar mesin beroperasi aman</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto">
                <!-- Allowed -->
                <div class="p-6 rounded-2xl bg-emerald-50/50 border border-emerald-200">
                    <div class="flex items-center gap-2 text-emerald-900 font-bold text-sm mb-4">
                        <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>BOLEH DIMASUKKAN:</span>
                    </div>
                    <ul class="space-y-2 text-xs text-slate-700">
                        <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Sisa sayuran mentah maupun matang</li>
                        <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Kulit buah-buahan (pisang, pepaya, apel, dll)</li>
                        <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Ampas kopi dan kantong teh (tanpa tali/staples)</li>
                        <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Sisa nasi, roti, dan olahan tepung</li>
                        <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Daun kering dan cacahan ranting kecil</li>
                        <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Cangkang telur yang dihancurkan</li>
                    </ul>
                </div>

                <!-- Forbidden -->
                <div class="p-6 rounded-2xl bg-rose-50/50 border border-rose-200">
                    <div class="flex items-center gap-2 text-rose-900 font-bold text-sm mb-4">
                        <svg class="w-5 h-5 text-rose-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>DILARANG KERAS DIMASUKKAN:</span>
                    </div>
                    <ul class="space-y-2 text-xs text-slate-700">
                        <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span> Tulang sapi / kambing yang besar dan keras</li>
                        <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span> Plastik, kantong kresek, dan styrofoam</li>
                        <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span> Logam, sendok, kawat, pecahan kaca, atau baterai</li>
                        <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span> Minyak goreng jelantah dalam volume banyak</li>
                        <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span> Kotoran hewan peliharaan (kucing/anjing)</li>
                        <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span> Puntung rokok dan abu kimia</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const devices = @json($devicesForMap);
            const defaultLat = devices.length > 0 ? devices[0].lat : -6.597147;
            const defaultLng = devices.length > 0 ? devices[0].lng : 106.806039;

            const map = L.map('homeMap', {
                scrollWheelZoom: false
            }).setView([defaultLat, defaultLng], 12);

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
                    <div style="font-family: inherit; font-size: 12px;">
                        <strong style="color: #0f172a;">${d.code}</strong><br>
                        <span>Status: <strong>${d.status_label}</strong></span><br>
                        <span style="color: #64748b; font-size: 11px;">${d.location}</span>
                    </div>
                `);
            });
        });
    </script>
</x-public-layout>
