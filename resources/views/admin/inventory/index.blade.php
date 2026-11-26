<x-admin-layout title="Inventaris & Distribusi" headerTitle="Inventaris & Distribusi Alat" headerSubtitle="Kelola unit alat unik, alokasi kota, dan distribusi RT/RW">

    @if ($pendingRequest)
        @php
            $isCityRequest = $pendingRequest->neighborhood_id === null;
            $poolDevices = $isCityRequest ? $centralAvailableDevices : $cityAvailableDevices;
            $formAction = $isCityRequest ? route('admin.inventory.allocate-to-city') : route('admin.inventory.distribute-rtrw');
            $targetLabel = $isCityRequest
                ? ($pendingRequest->city->name ?? 'Kota Tujuan')
                : ('RT ' . ($pendingRequest->neighborhood->rt_number ?? '-') . ' / RW ' . ($pendingRequest->neighborhood->rw_number ?? '-') . ' (' . ($pendingRequest->city->name ?? '') . ')');
        @endphp

        <!-- FORM DISTRIBUSI OTOMATIS DARI PENGAJUAN YANG DISETUJUI -->
        <div class="mb-6 bg-emerald-50/70 border-2 border-emerald-400 rounded-2xl p-5 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                <div>
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-600 text-white uppercase tracking-wider mb-1">
                        Pengajuan Disetujui — Siap Kirim
                    </span>
                    <h2 class="text-base font-bold text-slate-900">Form Distribusi Pengajuan #{{ $pendingRequest->id }}</h2>
                    <p class="text-xs text-slate-600">
                        Tujuan: <strong>{{ $targetLabel }}</strong> &bull; Diminta: <strong class="font-mono-numeric">{{ $pendingRequest->requested_count }} Unit</strong>
                    </p>
                </div>
                <a href="{{ route('admin.inventory.index') }}" class="text-xs text-slate-500 hover:text-slate-800 underline">Batal / Tutup</a>
            </div>

            @if ($poolDevices->count() < $pendingRequest->requested_count)
                <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800 mb-3">
                    Stok tersedia saat ini hanya <strong>{{ $poolDevices->count() }} unit</strong> (kurang dari permintaan {{ $pendingRequest->requested_count }} unit). Tambahkan alat terlebih dahulu atau kirim stok yang tersedia.
                </div>
            @endif

            <form method="POST" action="{{ $formAction }}" class="space-y-4">
                @csrf
                <input type="hidden" name="device_request_id" value="{{ $pendingRequest->id }}">
                @if ($isCityRequest)
                    <input type="hidden" name="city_id" value="{{ $pendingRequest->city_id }}">
                @else
                    <input type="hidden" name="neighborhood_id" value="{{ $pendingRequest->neighborhood_id }}">
                @endif

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">
                        Alat Terpilih Otomatis (Acak) — Tersedia {{ $poolDevices->count() }} Unit:
                    </label>
                    @if ($poolDevices->isEmpty())
                        <div class="p-3 rounded-xl bg-white border border-slate-200 text-xs text-slate-500">
                            Belum ada unit alat yang tersedia di stok.
                        </div>
                    @else
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 max-h-44 overflow-y-auto p-2 bg-white rounded-xl border border-emerald-200">
                            @foreach ($poolDevices as $avail)
                                <label class="flex items-center gap-2 p-2 rounded-lg border border-slate-200 hover:bg-emerald-50/50 text-xs cursor-pointer">
                                    <input type="checkbox" name="device_ids[]" value="{{ $avail->id }}"
                                        {{ in_array($avail->id, $preselectedDeviceIds) ? 'checked' : '' }}
                                        class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                    <span class="font-bold font-mono-numeric text-slate-800 truncate">{{ $avail->device_code }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-end gap-3">
                    <button type="submit" {{ $poolDevices->isEmpty() ? 'disabled' : '' }}
                        class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white font-semibold text-xs shadow-xs transition">
                        Kirim & Distribusikan Alat Sekarang
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- 1. STOCK SUMMARY CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        @if (auth()->user()->isMasterAdmin())
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Stok Gudang Pusat</span>
                <div class="text-3xl font-extrabold text-slate-900 font-mono-numeric mt-2">
                    {{ $centralStock }}
                    <span class="text-xs font-normal text-slate-500 ml-1">Unit Tersedia</span>
                </div>
            </div>
        @else
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Stok Gudang Kota ({{ auth()->user()->city->name ?? '' }})</span>
                <div class="text-3xl font-extrabold text-emerald-700 font-mono-numeric mt-2">
                    {{ $cityStock }}
                    <span class="text-xs font-normal text-slate-500 ml-1">Unit Siap Kirim</span>
                </div>
            </div>
        @endif

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Unit Terdaftar</span>
            <div class="text-3xl font-extrabold text-slate-900 font-mono-numeric mt-2">
                {{ $devices->total() }}
                <span class="text-xs font-normal text-slate-500 ml-1">Unit</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Standar Identitas Alat</span>
            <div class="text-lg font-bold text-slate-900 mt-2">
                1 Nama = 1 Unit Unik
            </div>
        </div>
    </div>

    <!-- 2. ACTION FORMS ACCORDING TO ROLE -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        @if (auth()->user()->isMasterAdmin())
            <!-- Form 1: Tambah Alat Baru (Master Admin) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <h3 class="text-sm font-bold text-slate-900 mb-1">Tambah Alat Baru</h3>
                <p class="text-xs text-slate-500 mb-4">Setiap alat wajib memiliki nama unik yang berbeda</p>

                <form method="POST" action="{{ route('admin.inventory.add-central-stock') }}" class="space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Nama Alat</label>
                        <input type="text" name="device_name" value="{{ old('device_name') }}" placeholder="Contoh: SGC-A01" required
                            class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-emerald-600">
                        @error('device_name')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Catatan</label>
                        <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Keterangan pengadaan (opsional)"
                            class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-emerald-600">
                    </div>
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-xs transition">
                        Simpan Alat ke Stok Pusat
                    </button>
                </form>
            </div>

            <!-- Form 2: Alokasi Acak ke Kota (Master Admin) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <h3 class="text-sm font-bold text-slate-900 mb-1">Alokasi Alat ke Kota / Kabupaten</h3>
                <p class="text-xs text-slate-500 mb-4">Unit dikirim secara acak dari stok pusat ({{ $centralStock }} tersedia)</p>

                <form method="POST" action="{{ route('admin.inventory.allocate-to-city') }}" class="space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Kota Tujuan</label>
                        <select name="city_id" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-emerald-600">
                            <option value="">-- Pilih Kota --</option>
                            @foreach ($cities as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->province->name ?? '' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Jumlah Dikirim (Acak)</label>
                        <input type="number" name="quantity" min="1" max="{{ max(1, $centralStock) }}" value="1" required
                            class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-emerald-600 font-mono-numeric">
                    </div>
                    <button type="submit" {{ $centralStock < 1 ? 'disabled' : '' }}
                        class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 disabled:opacity-50 text-white font-semibold text-xs shadow-xs transition">
                        {{ $centralStock < 1 ? 'Stok Pusat Kosong' : 'Kirim Alokasi Acak ke Kota' }}
                    </button>
                </form>
            </div>
        @else
            <!-- Form Distribusi ke RT/RW (Admin Kota) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <h3 class="text-sm font-bold text-slate-900 mb-1">Distribusi Alat ke RT/RW</h3>
                <p class="text-xs text-slate-500 mb-4">Unit dipilih acak dari stok kota ({{ $cityStock }} tersedia)</p>

                <form method="POST" action="{{ route('admin.inventory.distribute-rtrw') }}" class="space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Lingkungan RT/RW Tujuan</label>
                        <select name="neighborhood_id" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-emerald-600">
                            <option value="">-- Pilih RT/RW --</option>
                            @foreach ($neighborhoods as $n)
                                <option value="{{ $n->id }}">{{ $n->full_label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Jumlah Dikirim (Acak)</label>
                        <input type="number" name="quantity" min="1" max="{{ max(1, $cityStock) }}" value="1" required
                            class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-emerald-600 font-mono-numeric">
                    </div>
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white font-semibold text-xs shadow-xs transition"
                        {{ $cityStock < 1 ? 'disabled' : '' }}>
                        {{ $cityStock < 1 ? 'Stok Kota Habis' : 'Kirim & Pasang Unit' }}
                    </button>
                </form>
            </div>

            <!-- Pengajuan Stok ke Pusat -->
            <div class="bg-slate-50 rounded-2xl border border-slate-200/80 p-5 flex flex-col justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 mb-1">Butuh Tambahan Stok Kota?</h3>
                    <p class="text-xs text-slate-600 leading-relaxed mt-2">
                        Jika stok kota habis, ajukan permohonan alat ke Master Admin melalui menu Pengajuan Unit Alat.
                    </p>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.device-requests.index') }}"
                        class="inline-flex items-center px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold transition">
                        Buka Pengajuan Unit Alat &rarr;
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- 3. DEVICES LIST & RECENT TRANSACTIONS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Devices Table (Col-Span 2) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs" x-data="{ editingId: null }">
            <h3 class="text-sm font-bold text-slate-900 mb-4">Daftar Seluruh Unit Alat</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200/80 text-slate-500 uppercase tracking-wider text-[10px]">
                            <th class="pb-3 font-semibold">Nama Alat</th>
                            <th class="pb-3 font-semibold">Penempatan</th>
                            <th class="pb-3 font-semibold">Status</th>
                            <th class="pb-3 font-semibold">Sensor</th>
                            @if (auth()->user()->isMasterAdmin())
                                <th class="pb-3 font-semibold text-right">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($devices as $dev)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3">
                                    <div x-show="editingId !== {{ $dev->id }}">
                                        <div class="font-bold font-mono-numeric text-slate-900">{{ $dev->device_code }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono-numeric">{{ $dev->serial_number }}</div>
                                    </div>
                                    @if (auth()->user()->isMasterAdmin())
                                        <form x-show="editingId === {{ $dev->id }}" method="POST" action="{{ route('admin.inventory.update-device', $dev->id) }}" class="flex items-center gap-1.5">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="device_name" value="{{ $dev->device_code }}" required
                                                class="w-32 rounded-lg border border-slate-300 px-2 py-1 text-xs font-mono-numeric">
                                            <button type="submit" class="px-2 py-1 rounded bg-emerald-600 text-white text-[10px] font-semibold">Simpan</button>
                                            <button type="button" @click="editingId = null" class="px-2 py-1 rounded bg-slate-200 text-slate-700 text-[10px]">Batal</button>
                                        </form>
                                    @endif
                                </td>
                                <td class="py-3">
                                    @if ($dev->neighborhood)
                                        <div class="text-slate-800">RT {{ $dev->neighborhood->rt_number }} / RW {{ $dev->neighborhood->rw_number }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $dev->city->name ?? '-' }}</div>
                                    @elseif ($dev->city)
                                        <div class="text-slate-800 font-medium">Gudang {{ $dev->city->name }}</div>
                                        <div class="text-[10px] text-slate-400">Belum dipasang ke RT/RW</div>
                                    @else
                                        <div class="text-emerald-700 font-medium">Gudang Pusat</div>
                                        <div class="text-[10px] text-slate-400">Siap alokasi</div>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold {{ $dev->current_status->badgeClasses() }}">
                                        {{ $dev->current_status->label() }}
                                    </span>
                                </td>
                                <td class="py-3 font-mono-numeric text-[11px] text-slate-700">
                                    {{ $dev->current_temperature }}°C • {{ $dev->current_weight }}Kg
                                </td>
                                @if (auth()->user()->isMasterAdmin())
                                    <td class="py-3 text-right">
                                        <div class="inline-flex items-center gap-1.5">
                                            <button type="button" @click="editingId = {{ $dev->id }}"
                                                class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-semibold">
                                                Edit
                                            </button>
                                            @if ($dev->neighborhood_id === null)
                                                <form method="POST" action="{{ route('admin.inventory.destroy-device', $dev->id) }}"
                                                    onsubmit="return confirm('Hapus alat {{ $dev->device_code }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-2 py-1 rounded bg-rose-50 hover:bg-rose-100 text-rose-700 text-[10px] font-semibold">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">Belum ada unit alat terdaftar.</td>
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
            <h3 class="text-sm font-bold text-slate-900 mb-4">Riwayat Mutasi Alat</h3>

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
