<x-public-layout title="Teknologi & Fitur — Smart Green Composter">

    <!-- Header Section -->
    <section class="bg-gradient-to-b from-emerald-50/70 via-white to-white py-14 border-b border-slate-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 mb-3">
                Spesifikasi & Arsitektur
            </span>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                Teknologi & Fitur Cerdas
            </h1>
            <p class="mt-3 text-sm text-slate-600 max-w-xl mx-auto leading-relaxed">
                Integrasi perangkat keras tangguh, mikrokontroler ESP32, telemetri real-time, dan kecerdasan buatan terpadu.
            </p>
        </div>
    </section>

    <!-- Tech Grid -->
    <section class="py-14 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <!-- Hardware Specs -->
            <div>
                <h2 class="text-xs font-bold text-emerald-700 tracking-widest uppercase mb-2">Komponen Keras</h2>
                <h3 class="text-xl font-bold text-slate-900 mb-6">Arsitektur Perangkat IoT Smart Green Composter</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-800 font-bold text-xs flex items-center justify-center mb-3">
                            MCU
                        </div>
                        <h4 class="text-sm font-bold text-slate-900 mb-1">ESP32 Dual-Core SoC</h4>
                        <p class="text-xs text-slate-600 leading-relaxed mb-3">
                            Mikrokontroler 240MHz dengan konektivitas Wi-Fi terintegrasi. Menjalankan logika timer lokal dan failsafe shutdown independen tanpa tergantung internet.
                        </p>
                        <div class="text-[10px] text-slate-400 font-mono-numeric">Failsafe: Firmware Cutoff 70°C</div>
                    </div>

                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-800 font-bold text-xs flex items-center justify-center mb-3">
                            TEMP
                        </div>
                        <h4 class="text-sm font-bold text-slate-900 mb-1">Sensor Suhu DHT22</h4>
                        <p class="text-xs text-slate-600 leading-relaxed mb-3">
                            Sensor presisi tinggi dengan akurasi &plusmn;0.5°C untuk memonitor fase termofilik pengomposan dan mencegah overheating pada tabung pengolahan.
                        </p>
                        <div class="text-[10px] text-slate-400 font-mono-numeric">Rentang: -40°C hingga +80°C</div>
                    </div>

                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-800 font-bold text-xs flex items-center justify-center mb-3">
                            SCALE
                        </div>
                        <h4 class="text-sm font-bold text-slate-900 mb-1">LoadCell HX711 24-bit</h4>
                        <p class="text-xs text-slate-600 leading-relaxed mb-3">
                            Modul amplifikasi ADC 24-bit untuk penimbangan sampah awal dan kompos akhir secara otomatis. Menghitung persentase reduksi massa akurat.
                        </p>
                        <div class="text-[10px] text-slate-400 font-mono-numeric">Kapasitas: Maksimal 50.0 Kg</div>
                    </div>
                </div>
            </div>

            <!-- Software Features -->
            <div class="pt-8 border-t border-slate-100">
                <h2 class="text-xs font-bold text-emerald-700 tracking-widest uppercase mb-2">Fitur Perangkat Lunak</h2>
                <h3 class="text-xl font-bold text-slate-900 mb-6">Platform Web & Keamanan Data Terintegrasi</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="p-5 rounded-2xl bg-emerald-50/50 border border-emerald-100">
                        <h4 class="text-sm font-bold text-slate-900 mb-1">Peta Persebaran GIS Terdesentralisasi</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Peta interaktif berbasis OpenStreetMap dan Leaflet yang menampilkan sebaran unit komposter per kelurahan dan kota tanpa membocorkan data pribadi warga.
                        </p>
                    </div>

                    <div class="p-5 rounded-2xl bg-emerald-50/50 border border-emerald-100">
                        <h4 class="text-sm font-bold text-slate-900 mb-1">Audit Log Append-Only Tamper-Evident</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Setiap perintah mesin dicatat dalam rantai hash SHA-256 yang terhubung (hash chaining), mencegah manipulasi data rekam jejak operasional.
                        </p>
                    </div>

                    <div class="p-5 rounded-2xl bg-emerald-50/50 border border-emerald-100">
                        <h4 class="text-sm font-bold text-slate-900 mb-1">Alur Persetujuan Bertingkat (Approval Flow)</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Petugas lapangan dan admin kota wajib mengajukan izin operasional kepada Admin RT/RW sebelum mesin dapat dioperasikan.
                        </p>
                    </div>

                    <div class="p-5 rounded-2xl bg-emerald-50/50 border border-emerald-100">
                        <h4 class="text-sm font-bold text-slate-900 mb-1">Asisten AI Edukasi Publik (RAG)</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Chatbot publik siap menjawab pertanyaan warga seputar klasifikasi sampah yang boleh/dilarang dimasukkan dan panduan pengomposan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-public-layout>
