<x-admin-layout title="Jadwal Petugas Operasional" headerTitle="Jadwal & Manajemen Petugas Lapangan" headerSubtitle="Pengaturan jadwal giliran operasional mesin pengolah sampah di lingkungan RT/RW">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        @if (auth()->user()->isAdminRtRw())
            <!-- Form 1: Tambah Petugas Lapangan Baru -->
            <div class="space-y-6">
                <!-- Buat Akun Petugas -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                    <h3 class="text-sm font-bold text-slate-900 mb-1">Tambah Akun Petugas Lapangan</h3>
                    <p class="text-xs text-slate-500 mb-3">Daftarkan petugas operator mesin di lingkungan RT/RW Anda</p>

                    <form method="POST" action="{{ route('admin.schedules.create-operator') }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700">Nama Petugas</label>
                            <input type="text" name="name" required placeholder="Contoh: Bpk. Dedi"
                                class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:border-emerald-600">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700">Email Petugas</label>
                            <input type="email" name="email" required placeholder="petugas2@zerowaste.id"
                                class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:border-emerald-600">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700">No. HP / WhatsApp</label>
                            <input type="tel" name="phone_number" required placeholder="08123456789"
                                class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:border-emerald-600">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700">Kata Sandi</label>
                            <input type="password" name="password" required value="password"
                                class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:border-emerald-600">
                        </div>
                        <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs shadow-xs transition">
                            Buat Akun Petugas
                        </button>
                    </form>
                </div>

                <!-- Form Buat Jadwal Tugas -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                    <h3 class="text-sm font-bold text-slate-900 mb-1">Tetapkan Jadwal Tugas</h3>
                    <p class="text-xs text-slate-500 mb-3">Tentukan petugas piket operasional mesin</p>

                    <form method="POST" action="{{ route('admin.schedules.store') }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700">Pilih Petugas Lapangan</label>
                            <select name="operator_id" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:border-emerald-600">
                                <option value="">-- Pilih Petugas --</option>
                                @foreach ($operators as $op)
                                    <option value="{{ $op->id }}">{{ $op->name }} ({{ $op->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700">Tanggal Piket</label>
                            <input type="date" name="duty_date" required value="{{ date('Y-m-d') }}"
                                class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:border-emerald-600">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700">Jam Mulai</label>
                                <input type="time" name="start_time" required value="08:00"
                                    class="mt-1 block w-full rounded-xl border border-slate-300 px-2 py-1.5 text-xs text-slate-900 focus:outline-none focus:border-emerald-600">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700">Jam Selesai</label>
                                <input type="time" name="end_time" required value="12:00"
                                    class="mt-1 block w-full rounded-xl border border-slate-300 px-2 py-1.5 text-xs text-slate-900 focus:outline-none focus:border-emerald-600">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700">Catatan Tugas</label>
                            <input type="text" name="notes" placeholder="Pencacahan sampah basah pasar RT..."
                                class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:border-emerald-600">
                        </div>
                        <button type="submit" class="w-full py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-xs transition">
                            Simpan Jadwal Operasional
                        </button>
                    </form>
                </div>
            </div>
        @else
            <!-- Notice Petugas Lapangan -->
            <div class="bg-emerald-50 rounded-2xl border border-emerald-200/80 p-5 flex flex-col justify-between">
                <div>
                    <h3 class="text-sm font-bold text-emerald-900 mb-1">Jadwal Tugas Anda</h3>
                    <p class="text-xs text-emerald-800 leading-relaxed">
                        Berikut adalah daftar jadwal giliran operasional mesin komposter yang ditugaskan kepada Anda oleh Ketua RT/RW. Pastikan melakukan penimbangan sampah awal dan akhir di menu Monitoring.
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-emerald-200 text-xs text-emerald-700 font-semibold">
                    Wilayah: {{ auth()->user()->jurisdiction_name }}
                </div>
            </div>
        @endif

        <!-- Tabel Jadwal (Col-Span 2) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
            <h3 class="text-sm font-bold text-slate-900 mb-1">Daftar Jadwal Operasional</h3>
            <p class="text-xs text-slate-500 mb-4">Agenda piket petugas pengolahan sampah</p>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200/80 text-slate-500 uppercase tracking-wider text-[10px]">
                            <th class="pb-3 font-semibold">Tanggal & Waktu</th>
                            <th class="pb-3 font-semibold">Petugas Lapangan</th>
                            <th class="pb-3 font-semibold">Wilayah RT / RW</th>
                            <th class="pb-3 font-semibold">Catatan Tugas</th>
                            @if (auth()->user()->isAdminRtRw())
                                <th class="pb-3 font-semibold text-right">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($schedules as $s)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5">
                                    <div class="font-bold text-slate-900 font-mono-numeric">{{ $s->duty_date->translatedFormat('l, d M Y') }}</div>
                                    <div class="text-[11px] text-slate-500 font-mono-numeric">{{ substr($s->start_time, 0, 5) }} - {{ substr($s->end_time, 0, 5) }} WIB</div>
                                </td>
                                <td class="py-3.5">
                                    <div class="font-bold text-slate-800">{{ $s->operator->name ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $s->operator->phone_number ?? '' }}</div>
                                </td>
                                <td class="py-3.5 text-slate-600">
                                    RT {{ $s->neighborhood->rt_number ?? '-' }} / RW {{ $s->neighborhood->rw_number ?? '-' }}
                                </td>
                                <td class="py-3.5 text-slate-500 text-[11px] max-w-xs truncate">
                                    {{ $s->notes ?? 'Piket rutin' }}
                                </td>
                                @if (auth()->user()->isAdminRtRw())
                                    <td class="py-3.5 text-right">
                                        <form method="POST" action="{{ route('admin.schedules.destroy', $s->id) }}"
                                            onsubmit="return confirm('Hapus jadwal ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">Belum ada jadwal yang ditetapkan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $schedules->links() }}
            </div>
        </div>
    </div>

</x-admin-layout>
