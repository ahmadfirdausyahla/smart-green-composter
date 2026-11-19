<x-admin-layout title="Kondisi Alat & Laporan" headerTitle="Status Kondisi Alat & Pelaporan Kerusakan" headerSubtitle="Pemantauan kesehatan sensor, riwayat gangguan mekanis, dan pelaporan kendala teknis">

    <!-- 1. HEALTH AND STATUS METRIC CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        @foreach ($devices as $dev)
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-900">{{ $dev->device_code }}</span>
                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold {{ $dev->health_status === \App\Enums\DeviceHealthStatus::HEALTHY ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200' }}">
                            {{ $dev->health_status->label() }}
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-500 mb-3">{{ $dev->model_name }} — RT {{ $dev->neighborhood->rt_number ?? '-' }}/RW {{ $dev->neighborhood->rw_number ?? '-' }}</div>

                    <div class="grid grid-cols-2 gap-2 text-xs py-2 border-y border-slate-100 mb-3">
                        <div>
                            <span class="text-slate-400 text-[10px] block">Sensor Suhu</span>
                            <span class="font-bold text-slate-800 font-mono-numeric">{{ $dev->current_temperature }} °C</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] block">Sensor Timbangan</span>
                            <span class="font-bold text-slate-800 font-mono-numeric">{{ $dev->current_weight }} Kg</span>
                        </div>
                    </div>
                </div>

                <div class="text-[10px] text-slate-400 flex items-center justify-between">
                    <span>Koneksi: {{ $dev->current_status->label() }}</span>
                    <span>{{ $dev->last_heartbeat_at ? $dev->last_heartbeat_at->diffForHumans() : 'No ping' }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <!-- 2. REPORT FORM & REPORT HISTORY -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- New Ticket Form (Col-Span 1) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
            <h3 class="text-sm font-bold text-slate-900 mb-1">Formulir Laporan Kerusakan Alat</h3>
            <p class="text-xs text-slate-500 mb-4">Kirimkan rincian kendala teknis untuk ditindaklanjuti Admin RT/RW atau Teknisi Kota</p>

            <form method="POST" action="{{ route('admin.maintenance.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="device_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Perangkat Bermasalah</label>
                    <select id="device_id" name="device_id" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:border-emerald-600 focus:outline-none">
                        @foreach ($devices as $d)
                            <option value="{{ $d->id }}">{{ $d->device_code }} (RT {{ $d->neighborhood->rt_number ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="category" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Kategori Gangguan</label>
                    <select id="category" name="category" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:border-emerald-600 focus:outline-none">
                        <option value="sensor_fault">Sensor Bermasalah (Suhu / Timbangan)</option>
                        <option value="motor_dead">Mesin / Motor Tidak Berputar</option>
                        <option value="temperature_abnormal">Suhu Tidak Normal / Overheat</option>
                        <option value="scale_inaccurate">Timbangan Tidak Akurat</option>
                        <option value="connectivity_lost">Koneksi WiFi / ESP32 Terputus</option>
                        <option value="mechanical">Kerusakan Mekanis Fisik</option>
                        <option value="other">Kendala Lainnya</option>
                    </select>
                </div>

                <div>
                    <label for="priority" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Tingkat Prioritas</label>
                    <select id="priority" name="priority" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:border-emerald-600 focus:outline-none">
                        <option value="low">Rendah (Pemeriksaan Berkala)</option>
                        <option value="medium" selected>Sedang (Operasional Terganggu)</option>
                        <option value="high">Tinggi (Mesin Tidak Beroperasi)</option>
                        <option value="critical">Kritis (Bahaya Overheat / Mekanis)</option>
                    </select>
                </div>

                <div>
                    <label for="description" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Deskripsi Masalah</label>
                    <textarea id="description" name="description" rows="3" required placeholder="Jelaskan gejala gangguan, suara motor, pembacaan display suhu..."
                        class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:border-emerald-600 focus:outline-none"></textarea>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-xs transition">
                    Kirim Laporan Kerusakan
                </button>
            </form>
        </div>

        <!-- Ticket History List (Col-Span 2) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
            <h3 class="text-sm font-bold text-slate-900 mb-1">Riwayat & Status Tindak Lanjut Laporan</h3>
            <p class="text-xs text-slate-500 mb-4">Daftar tiket kendala alat di wilayah kewenangan Anda</p>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200/80 text-slate-500 uppercase tracking-wider text-[10px]">
                            <th class="pb-3 font-semibold">No. Tiket</th>
                            <th class="pb-3 font-semibold">Perangkat</th>
                            <th class="pb-3 font-semibold">Kategori</th>
                            <th class="pb-3 font-semibold">Pelapor</th>
                            <th class="pb-3 font-semibold text-center">Prioritas</th>
                            <th class="pb-3 font-semibold text-center">Status</th>
                            @if (! auth()->user()->isAdminLapangan())
                                <th class="pb-3 font-semibold text-right">Tindak Lanjut</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($reports as $rep)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3 font-mono-numeric font-bold text-slate-900">{{ $rep->ticket_number }}</td>
                                <td class="py-3">{{ $rep->device->device_code }}</td>
                                <td class="py-3 text-slate-700">{{ str_replace('_', ' ', ucfirst($rep->category)) }}</td>
                                <td class="py-3 text-slate-600">{{ $rep->reporter->name ?? 'Petugas' }}</td>
                                <td class="py-3 text-center">
                                    <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold {{ $rep->priority === 'critical' ? 'bg-rose-100 text-rose-800' : ($rep->priority === 'high' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700') }}">
                                        {{ strtoupper($rep->priority) }}
                                    </span>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold {{ $rep->status === 'resolved' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                                        {{ strtoupper(str_replace('_', ' ', $rep->status)) }}
                                    </span>
                                </td>
                                @if (! auth()->user()->isAdminLapangan())
                                    <td class="py-3 text-right">
                                        @if ($rep->status !== 'resolved' && $rep->status !== 'closed')
                                            <form method="POST" action="{{ route('admin.maintenance.update-status', $rep->id) }}" class="inline-flex gap-1">
                                                @csrf
                                                <input type="hidden" name="status" value="resolved">
                                                <button type="submit" class="px-2 py-1 rounded bg-emerald-50 text-emerald-800 border border-emerald-200 text-[10px] font-semibold hover:bg-emerald-100">
                                                    Tandai Selesai
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[10px] text-slate-400">Terselesaikan</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">Belum ada laporan kerusakan pada unit ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $reports->links() }}
            </div>
        </div>
    </div>

</x-admin-layout>
