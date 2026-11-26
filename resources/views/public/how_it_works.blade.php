<x-public-layout title="Cara Kerja Mesin & Pengolahan — Smart Green Composter">

    <!-- Header Section -->
    <section class="bg-gradient-to-b from-emerald-50/70 via-white to-white py-14 border-b border-slate-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 mb-3">
                Proses Terstruktur 4 Tahap
            </span>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                Cara Kerja Smart Green Composter
            </h1>
            <p class="mt-3 text-sm text-slate-600 max-w-xl mx-auto leading-relaxed">
                Dari sampah dapur organik warga hingga menjadi pupuk kompos bernutrisi tinggi dalam siklus terukur 4 jam dengan panduan sensor cerdas.
            </p>
        </div>
    </section>

    <!-- Step by Step Workflow -->
    <section class="py-14 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative border-l-2 border-emerald-200 ml-4 md:ml-32 space-y-12">
                <!-- Step 1 -->
                <div class="relative pl-8 md:pl-10">
                    <div class="absolute -left-3.5 top-0 w-7 h-7 rounded-full bg-emerald-600 text-white font-bold text-xs flex items-center justify-center border-4 border-white shadow-xs">
                        1
                    </div>
                    <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-6">
                        <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider block mb-1">Tahap 1 — Penerimaan & Penimbangan Awal</span>
                        <h3 class="text-base font-bold text-slate-900 mb-2">Penyortiran Sampah Organik & Input ke Tabung</h3>
                        <p class="text-xs text-slate-600 leading-relaxed mb-3">
                            Warga membawa sisa sayur, buah, nasi, dan sisa bahan organik dapur ke pos komposter RT. Petugas memastikan sampah bebas dari plastik, logam, atau tulang keras besar. Sensor LoadCell HX711 langsung menimbang berat awal sampah secara presisi.
                        </p>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-white border border-slate-200 text-[11px] text-slate-600 font-mono-numeric">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Sensor: LoadCell HX711 &bull; Pencatatan Initial Weight
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="relative pl-8 md:pl-10">
                    <div class="absolute -left-3.5 top-0 w-7 h-7 rounded-full bg-emerald-600 text-white font-bold text-xs flex items-center justify-center border-4 border-white shadow-xs">
                        2
                    </div>
                    <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-6">
                        <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider block mb-1">Tahap 2 — Otorisasi & Perintah START</span>
                        <h3 class="text-base font-bold text-slate-900 mb-2">Persetujuan Operasi & Pengaturan Timer</h3>
                        <p class="text-xs text-slate-600 leading-relaxed mb-3">
                            Petugas lapangan mengajukan izin pengoperasian mesin melalui portal sistem, yang kemudian diverifikasi dan disetujui oleh Admin RT/RW. Setelah disetujui, perintah START dikirim dengan konfigurasi timer default (240 menit) dan batas aman suhu.
                        </p>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-white border border-slate-200 text-[11px] text-slate-600 font-mono-numeric">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Protokol: Role Approval Flow &bull; Durasi Standar: 240 Menit
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="relative pl-8 md:pl-10">
                    <div class="absolute -left-3.5 top-0 w-7 h-7 rounded-full bg-emerald-600 text-white font-bold text-xs flex items-center justify-center border-4 border-white shadow-xs">
                        3
                    </div>
                    <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-6">
                        <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider block mb-1">Tahap 3 — Proses Pencacahan & Pemanasan Terkontrol</span>
                        <h3 class="text-base font-bold text-slate-900 mb-2">Dekomposisi Cepat Termofilik</h3>
                        <p class="text-xs text-slate-600 leading-relaxed mb-3">
                            Motor memutar bilah pencacah secara berkala sembari pemanas mempertahankan suhu tabung pada rentang termofilik ideal (45°C – 62°C) untuk menguapkan kelembaban berlebih dan membunuh patogen. Proteksi independen firmware ESP32 otomatis mematikan pemanas jika suhu melewati batas 65°C.
                        </p>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-white border border-slate-200 text-[11px] text-slate-600 font-mono-numeric">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Proteksi: Hardware Cutoff pada 70°C &bull; Target Termofilik 55°C
                        </div>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="relative pl-8 md:pl-10">
                    <div class="absolute -left-3.5 top-0 w-7 h-7 rounded-full bg-emerald-600 text-white font-bold text-xs flex items-center justify-center border-4 border-white shadow-xs">
                        4
                    </div>
                    <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-6">
                        <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider block mb-1">Tahap 4 — Selesai & Perhitungan Reduksi Massa</span>
                        <h3 class="text-base font-bold text-slate-900 mb-2">Pengeluaran Kompos Matang & Pencatatan Resmi</h3>
                        <p class="text-xs text-slate-600 leading-relaxed mb-3">
                            Setelah timer selesai, sistem menimbang bobot akhir dan secara otomatis menghitung Reduksi Massa Selama Proses Pengolahan:
                            <br><code class="text-emerald-800 font-bold">((Bobot Awal - Bobot Akhir) / Bobot Awal) × 100%</code>.
                            Kompos kering dikeluarkan untuk pupuk tanaman warga atau program penghijauan lingkungan.
                        </p>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-[11px] text-emerald-800 font-mono-numeric font-bold">
                            Label Standar: Reduksi Massa Selama Proses Pengolahan (60% - 75%)
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-public-layout>
