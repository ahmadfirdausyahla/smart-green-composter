<x-admin-layout title="Inventaris Alat & Distribusi" headerTitle="Manajemen Inventaris & Distribusi Perangkat" headerSubtitle="Pengelolaan stok global, alokasi wilayah kota, dan distribusi ke lingkungan RT/RW">

    <!-- 1. STOCK SUMMARY CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        @if (auth()->user()->isMasterAdmin())
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Stok Pusat Nasional</span>
                <div class="text-3xl font-extrabold text-slate-900 font-mono-numeric mt-2">
                    {{ $centralInventories->whereNull('city_id')->sum('central_stock') }}
                    <span class="text-xs font-normal text-slate-500 ml-1">Unit Tersedia di Gudang Pusat</span>
                </div>
                <div class="mt-3 text-[11px] text-slate-500 border-t border-slate-100 pt-2">
                    Siap dialokasikan ke Dinas Lingkungan Hidup Kota/Kabupaten
                </div>
            </div>
        @else
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Stok Gudang Kota ({{ auth()->user()->city->name ?? '' }})</span>
                <div class="text-3xl font-extrabold text-emerald-700 font-mono-numeric mt-2">
                    {{ $cityStock }}
                    <span class="text-xs font-normal text-slate-500 ml-1">Unit Siap Distribusi</span>
                </div>
                <div class="mt-3 text-[11px] text-slate-500 border-t border-slate-100 pt-2">
                    Dapat langsung dipasang ke lingkungan RT/RW yang mengajukan
                </div>
            </div>
        @endif

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Unit Terpasang</span>
            <div class="text-3xl font-extrabold text-slate-900 font-mono-numeric mt-2">
                {{ $devices->total() }}
                <span class="text-xs font-normal text-slate-500 ml-1">Unit Beroperasi</span>
            </div>
            <div class="mt-3 text-[11px] text-slate-500 border-t border-slate-100 pt-2">
                Aktif mengirim data telemetri suhu & timbangan
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Tipe Model Standar</span>
            <div class="text-lg font-bold text-slate-900 mt-2">
                Smart Green Composter v1 Pro
            </div>
            <div class="mt-3 text-[11px] text-slate-500 border-t border-slate-100 pt-2">
                Kapasitas 50 Kg / siklus • ESP32 IoT Engine
            </div>
        </div>
    </div>

    <!-- 2. ACTION FORMS ACCORDING TO ROLE -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        @if (auth()->user()->isMasterAdmin())
            <!-- Form 1: Tambah Stok Pusat (Master Admin) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <h3 class="text-sm font-bold text-slate-900 mb-1">Tambah Stok Inventaris Pusat</h3>
                <p class="text-xs text-slate-500 mb-4">Penerimaan batch unit baru dari vendor manufaktur</p>

                <form method="POST" action="{{ route('admin.inventory.add-central-stock') }}" class="space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Model Perangkat</label>
                        <input type="text" name="model_name" value="Smart Green Composter v1 Pro" required
                            class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-emerald-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Jumlah Unit</label>
                        <input type="number" name="quantity" min="1" max="500" value="10" required
                            class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-emerald-600 font-mono-numeric">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Catatan Pengadaan</label>
                        <input type="text" name="notes" placeholder="Batch pengadaan APBN/CSR..."
                            class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-emerald-600">
                    </div>
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-xs transition">
                        Tambahkan ke Stok Pusat
                    </button>
                </form>
            </div>

            <!-- Form 2: Alokasi ke Kota (Master Admin) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <h3 class="text-sm font-bold text-slate-900 mb-1">Alokasikan Unit ke Kota / Kabupaten</h3>
                <p class="text-xs text-slate-500 mb-4">Kirimkan stok dari pusat ke gudang dinas wilayah kota</p>

                <form method="POST" action="{{ route('admin.inventory.allocate-to-city') }}" class="space-y-3.5">
                    @csrf
                    <input type="hidden" name="model_name" value="Smart Green Composter v1 Pro">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Pilih Kota Tujuan</label>
                        <select name="city_id" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-emerald-600">
                            <option value="">-- Pilih Kota --</option>
                            @foreach ($cities as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->province->name ?? '' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Jumlah Unit yang Dialokasikan</label>
                        <input type="number" name="quantity" min="1" max="100" value="5" required
                            class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-emerald-600 font-mono-numeric">
                    </div>
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs shadow-xs transition">
                        Kirim Alokasi ke Gudang Kota
                    </button>
                </form>
            </div>
        @else
            <!-- Form Distribusi ke RT/RW (Admin Kota) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <h3 class="text-sm font-bold text-slate-900 mb-1">Distribusi Unit ke Lingkungan RT/RW</h3>
                <p class="text-xs text-slate-500 mb-4">Pasang dan aktifkan unit baru dari stok kota ke RT/RW</p>

                <form method="POST" action="{{ route('admin.inventory.distribute-to-rtrw') }}" class="space-y-3.5">
                    @csrf
                    <input type="hidden" name="model_name" value="Smart Green Composter v1 Pro">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Pilih Lingkungan RT/RW Tujuan</label>
                        <select name="neighborhood_id" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-emerald-600">
                            <option value="">-- Pilih RT/RW --</option>
                            @foreach ($neighborhoods as $n)
                                <option value="{{ $n->id }}">{{ $n->full_label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 text-xs text-slate-600">
                        Sisa stok kota saat ini: <strong class="text-emerald-700 font-mono-numeric">{{ $cityStock }} Unit</strong>. Perangkat akan otomatis dibuatkan API Key ESP32 unik dan kode registrasi resmi.
                    </div>
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-xs transition"
                        {{ $cityStock < 1 ? 'disabled' : '' }}>
                        {{ $cityStock < 1 ? 'Stok Kota Habis' : 'Distribusikan & Pasang Unit' }}
                    </button>
                </form>
            </div>

            <!-- Panduan Pengajuan Stok -->
            <div class="bg-slate-50 rounded-2xl border border-slate-200/80 p-5 flex flex-col justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 mb-1">Pengajuan Penambahan Stok ke Pusat</h3>
                    <p class="text-xs text-slate-500 mb-3">Jika stok unit di Kota {{ auth()->user()->city->name ?? '' }} menipis</p>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Admin Kota dapat mengajukan permohonan alokasi tambahan kepada Master Admin ZeroWaste Green untuk mendukung perluasan program komposting di lingkungan RT/RW binaan.
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-200 text-xs text-slate-500">
                    Hubungi Master Admin pusat untuk evaluasi kuota wilayah.
                </div>
            </div>
        @endif
    </div>

    <!-- 3. DEVICES LIST & RECENT TRANSACTIONS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Devices Table (Col-Span 2) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
            <h3 class="text-sm font-bold text-slate-900 mb-1">Daftar Seluruh Unit Mesin Terdaftar</h3>
            <p class="text-xs text-slate-500 mb-4">Informasi perangkat fisik, status koneksi, dan penempatan wilayah</p>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200/80 text-slate-500 uppercase tracking-wider text-[10px]">
                            <th class="pb-3 font-semibold">Kode & Serial</th>
                            <th class="pb-3 font-semibold">Wilayah Penempatan</th>
                            <th class="pb-3 font-semibold">Status</th>
                            <th class="pb-3 font-semibold">Sensor</th>
                            <th class="pb-3 font-semibold text-right">Tanggal Pasang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($devices as $dev)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3">
                                    <div class="font-bold font-mono-numeric text-slate-900">{{ $dev->device_code }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono-numeric">{{ $dev->serial_number }}</div>
                                </td>
                                <td class="py-3">
                                    <div class="text-slate-800">RT {{ $dev->neighborhood->rt_number ?? '-' }} / RW {{ $dev->neighborhood->rw_number ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $dev->city->name ?? '-' }}</div>
                                </td>
                                <td class="py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold {{ $dev->current_status->badgeClasses() }}">
                                        {{ $dev->current_status->label() }}
                                    </span>
                                </td>
                                <td class="py-3 font-mono-numeric text-[11px] text-slate-700">
                                    {{ $dev->current_temperature }}°C • {{ $dev->current_weight }}Kg
                                </td>
                                <td class="py-3 text-right font-mono-numeric text-slate-500 text-[11px]">
                                    {{ $dev->installed_at ? $dev->installed_at->format('d/m/Y') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">Belum ada unit terpasang.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $devices->links() }}
            </div>
        </div>

        <!-- Recent Inventory Transactions (Col-Span 1) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
            <h3 class="text-sm font-bold text-slate-900 mb-1">Riwayat Log Inventaris</h3>
            <p class="text-xs text-slate-500 mb-4">Audit perpindahan dan alokasi unit mesin</p>

            <div class="space-y-3">
                @forelse ($transactions as $tx)
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-bold text-slate-800">{{ strtoupper(str_replace('_', ' ', $tx->transaction_type)) }}</span>
                            <span class="text-[10px] text-slate-400 font-mono-numeric">{{ $tx->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="text-[11px] text-slate-600">
                            Dari: <strong>{{ $tx->from_entity }}</strong><br>
                            Ke: <strong>{{ $tx->to_entity }}</strong>
                        </div>
                        <div class="text-[10px] text-slate-400 mt-1 truncate">Oleh: {{ $tx->performedBy->name ?? 'System' }}</div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400 text-xs">Belum ada catatan mutasi inventaris.</div>
                @endforelse
            </div>
        </div>
    </div>

</x-admin-layout>
