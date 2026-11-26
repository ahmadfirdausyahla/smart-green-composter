<x-public-layout title="Hubungi Kami — Smart Green Composter">

    <!-- Header Section -->
    <section class="bg-gradient-to-b from-emerald-50/70 via-white to-white py-14 border-b border-slate-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 mb-3">
                Layanan Informasi & Kemitraan
            </span>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                Hubungi Tim ZeroWaste Green
            </h1>
            <p class="mt-3 text-sm text-slate-600 max-w-xl mx-auto leading-relaxed">
                Kami siap mendampingi komunitas RT, RW, dan pemerintah daerah dalam program transformasi pengolahan sampah organik terpadu.
            </p>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-14 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <!-- Contact Info -->
                <div class="space-y-6">
                    <div>
                        <h2 class="text-xs font-bold text-emerald-700 tracking-widest uppercase mb-2">Informasi Kantor</h2>
                        <h3 class="text-xl font-bold text-slate-900 mb-4">Sekretariat ZeroWaste Green</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Pusat koordinasi implementasi Smart Green Composter, kemitraan kota percontohan, dan laboratorium uji sensor mikrokontroler IoT.
                        </p>
                    </div>

                    <div class="space-y-4 text-xs">
                        <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <span class="font-bold text-slate-900 block mb-0.5">Alamat Kantor</span>
                                <span class="text-slate-600">Gedung Inovasi Lingkungan Lt. 3, Jl. Lodaya No. 12, Babakan, Kota Bogor, Jawa Barat 16128</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <span class="font-bold text-slate-900 block mb-0.5">Email Dukungan & Kemitraan</span>
                                <span class="text-slate-600 font-mono-numeric">kontak@zerowastegreen.id &bull; support@zerowaste.id</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div>
                                <span class="font-bold text-slate-900 block mb-0.5">WhatsApp & Hotline Tim ZeroWaste</span>
                                <a href="https://wa.me/6281615160101" target="_blank" rel="noopener noreferrer" class="text-emerald-700 hover:text-emerald-800 font-semibold font-mono-numeric">
                                    0816-1516-0101 (+62 816-1516-0101)
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Permohonan Demo / Sosialisasi -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
                    <h3 class="text-sm font-bold text-slate-900 mb-1">Form Hubungi Tim ZeroWaste Green</h3>
                    <p class="text-xs text-slate-500 mb-5">Pesan akan langsung diteruskan ke WhatsApp resmi Tim ZeroWaste (0816-1516-0101)</p>

                    <form onsubmit="submitToWhatsApp(event, this)" class="space-y-4 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap Pemohon</label>
                            <input type="text" name="nama" required placeholder="Contoh: Budi Santoso"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs focus:outline-none focus:border-emerald-600">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Nomor WhatsApp / HP</label>
                                <input type="tel" name="wa" required placeholder="0812xxxxxxxx"
                                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs focus:outline-none focus:border-emerald-600">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Kota / Kabupaten</label>
                                <input type="text" name="kota" required placeholder="Contoh: Kota Bogor"
                                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs focus:outline-none focus:border-emerald-600">
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Asal Komunitas / RT / RW</label>
                            <input type="text" name="komunitas" required placeholder="Contoh: Pengurus RT 03 / RW 05 Babakan"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs focus:outline-none focus:border-emerald-600">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Catatan / Kebutuhan Sosialisasi</label>
                            <textarea name="catatan" rows="3" placeholder="Sebutkan perkiraan jumlah kepala keluarga dan jadwal yang diharapkan..."
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs focus:outline-none focus:border-emerald-600"></textarea>
                        </div>

                        <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-xs transition">
                            Kirim ke WhatsApp Tim ZeroWaste (081615160101)
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script>
        function submitToWhatsApp(event, form) {
            event.preventDefault();
            const nama = form.nama.value.trim();
            const wa = form.wa.value.trim();
            const kota = form.kota.value.trim();
            const komunitas = form.komunitas.value.trim();
            const catatan = form.catatan.value.trim() || '-';

            const text = `Halo Tim ZeroWaste Green,%0A%0ASaya ingin menghubungi terkait Smart Green Composter:%0A- Nama: ${encodeURIComponent(nama)}%0A- No. HP/WA: ${encodeURIComponent(wa)}%0A- Kota/Kabupaten: ${encodeURIComponent(kota)}%0A- Asal Komunitas/RT/RW: ${encodeURIComponent(komunitas)}%0A- Catatan: ${encodeURIComponent(catatan)}`;
            window.open(`https://wa.me/6281615160101?text=${text}`, '_blank', 'noopener,noreferrer');
            form.reset();
        }
    </script>

</x-public-layout>
