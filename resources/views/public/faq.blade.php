<x-public-layout title="FAQ — Smart Green Composter">

    <!-- Header Section -->
    <section class="bg-gradient-to-b from-emerald-50/70 via-white to-white py-14 border-b border-slate-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 mb-3">
                Pertanyaan yang Sering Diajukan
            </span>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                Frequently Asked Questions (FAQ)
            </h1>
            <p class="mt-3 text-sm text-slate-600 max-w-xl mx-auto leading-relaxed">
                Jawaban praktis dan panduan operasional seputar program Smart Green Composter dan pengelolaan sampah organik terpadu.
            </p>
        </div>
    </section>

    <!-- FAQ Accordions -->
    <section class="py-14 bg-white" x-data="{ active: null }">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            <!-- Question 1 -->
            <div class="border border-slate-200/80 rounded-2xl overflow-hidden shadow-xs">
                <button @click="active = active === 1 ? null : 1" class="w-full p-5 text-left flex items-center justify-between bg-white hover:bg-slate-50 transition">
                    <span class="text-sm font-bold text-slate-900">Apa itu Smart Green Composter?</span>
                    <span class="text-emerald-700 font-bold text-base" x-text="active === 1 ? '−' : '+'">+</span>
                </button>
                <div x-show="active === 1" x-collapse class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3 bg-slate-50/50">
                    Smart Green Composter adalah mesin pengolah sampah organik pintar berbasis IoT yang memadukan pencacahan motorik dan pemanasan terkontrol untuk mempercepat dekomposisi sampah dapur menjadi kompos matang dalam hitungan jam, serta dilengkapi sensor timbangan LoadCell dan sensor suhu terintegrasi.
                </div>
            </div>

            <!-- Question 2 -->
            <div class="border border-slate-200/80 rounded-2xl overflow-hidden shadow-xs">
                <button @click="active = active === 2 ? null : 2" class="w-full p-5 text-left flex items-center justify-between bg-white hover:bg-slate-50 transition">
                    <span class="text-sm font-bold text-slate-900">Sampah apa saja yang BOLEH dan DILARANG dimasukkan ke mesin?</span>
                    <span class="text-emerald-700 font-bold text-base" x-text="active === 2 ? '−' : '+'">+</span>
                </button>
                <div x-show="active === 2" x-collapse class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3 bg-slate-50/50">
                    <p class="mb-2"><strong class="text-emerald-700">BOLEH:</strong> Sisa sayuran mentah/matang, kulit buah-buahan, ampas kopi dan teh, sisa nasi, daun kering, dan cangkang telur yang dihancurkan.</p>
                    <p><strong class="text-rose-700">DILARANG KERAS:</strong> Tulang hewan besar keras (sapi/kambing), plastik, kemasan styrofoam, puntung rokok, baterai, logam, cairan kimia, dan minyak goreng jenuh dalam jumlah besar.</p>
                </div>
            </div>

            <!-- Question 3 -->
            <div class="border border-slate-200/80 rounded-2xl overflow-hidden shadow-xs">
                <button @click="active = active === 3 ? null : 3" class="w-full p-5 text-left flex items-center justify-between bg-white hover:bg-slate-50 transition">
                    <span class="text-sm font-bold text-slate-900">Berapa lama durasi standar satu siklus pengolahan?</span>
                    <span class="text-emerald-700 font-bold text-base" x-text="active === 3 ? '−' : '+'">+</span>
                </button>
                <div x-show="active === 3" x-collapse class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3 bg-slate-50/50">
                    Durasi standar pengolahan adalah 240 menit (4 jam). Waktu ini dirancang secara optimal agar suhu termofilik dapat menguapkan kelembaban berlebih, mengurai bahan organik secara merata, dan menghasilkan reduksi massa rata-rata 60% – 75%.
                </div>
            </div>

            <!-- Question 4 -->
            <div class="border border-slate-200/80 rounded-2xl overflow-hidden shadow-xs">
                <button @click="active = active === 4 ? null : 4" class="w-full p-5 text-left flex items-center justify-between bg-white hover:bg-slate-50 transition">
                    <span class="text-sm font-bold text-slate-900">Bagaimana sistem keselamatan suhu bekerja?</span>
                    <span class="text-emerald-700 font-bold text-base" x-text="active === 4 ? '−' : '+'">+</span>
                </button>
                <div x-show="active === 4" x-collapse class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3 bg-slate-50/50">
                    Sistem memiliki batas operasional 65°C. Jika suhu melebihi 65°C, sistem memunculkan peringatan. Jika suhu menyentuh batas kritis 70°C, pemutus sirkuit firmware ESP32 secara mandiri melakukan shutdown pemanas seketika tanpa memerlukan konfirmasi jaringan atau browser pengguna.
                </div>
            </div>

            <!-- Question 5 -->
            <div class="border border-slate-200/80 rounded-2xl overflow-hidden shadow-xs">
                <button @click="active = active === 5 ? null : 5" class="w-full p-5 text-left flex items-center justify-between bg-white hover:bg-slate-50 transition">
                    <span class="text-sm font-bold text-slate-900">Bagaimana cara lingkungan RT/RW saya mendapatkan unit mesin?</span>
                    <span class="text-emerald-700 font-bold text-base" x-text="active === 5 ? '−' : '+'">+</span>
                </button>
                <div x-show="active === 5" x-collapse class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3 bg-slate-50/50">
                    Pengurus RT atau RW dapat mendaftarkan akun resmi melalui halaman <a href="{{ route('register.rtrw') }}" class="text-emerald-700 font-bold underline">Pendaftaran RT/RW</a>. Permohonan akan diverifikasi oleh Admin Kota setempat. Setelah disetujui, Admin RT/RW dapat mengajukan alokasi unit mesin dari stok inventaris kota.
                </div>
            </div>

            <!-- Dynamic Knowledge Documents Chunks (if any) -->
            @foreach ($documents as $doc)
                @foreach ($doc->chunks as $idx => $chunk)
                    <div class="border border-slate-200/80 rounded-2xl overflow-hidden shadow-xs">
                        <button @click="active = active === 'doc-{{ $doc->id }}-{{ $idx }}' ? null : 'doc-{{ $doc->id }}-{{ $idx }}'" class="w-full p-5 text-left flex items-center justify-between bg-white hover:bg-slate-50 transition">
                            <span class="text-sm font-bold text-slate-900">{{ $doc->title }} (Poin {{ $idx + 1 }})</span>
                            <span class="text-emerald-700 font-bold text-base" x-text="active === 'doc-{{ $doc->id }}-{{ $idx }}' ? '−' : '+'">+</span>
                        </button>
                        <div x-show="active === 'doc-{{ $doc->id }}-{{ $idx }}'" x-collapse class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3 bg-slate-50/50">
                            {{ $chunk->chunk_text }}
                        </div>
                    </div>
                @endforeach
            @endforeach

        </div>
    </section>

</x-public-layout>
