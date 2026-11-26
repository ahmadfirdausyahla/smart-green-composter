<x-admin-layout title="Monitoring & Remote Control" headerTitle="Industrial Monitoring & Remote Control" headerSubtitle="Panel kendali operasional mesin, telemetri sensor suhu, dan timbangan massa">

    <!-- 1. DEVICE SELECTOR BAR (Adaptive per Role) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 mb-6 shadow-xs">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Pemilihan Perangkat Aktif</span>
                <span class="text-xs text-slate-600">
                    @if (auth()->user()->isMasterAdmin())
                        Pilih perangkat dari seluruh Indonesia
                    @elseif (auth()->user()->isAdminKota())
                        Pilih unit di wilayah {{ auth()->user()->city->name ?? 'Kota' }}
                    @else
                        Unit komposter di wilayah {{ auth()->user()->neighborhood->full_label ?? 'RT/RW Anda' }}
                    @endif
                </span>
            </div>

            <!-- Device Dropdown / Selector -->
            <form method="GET" action="{{ route('admin.monitoring.index') }}" class="flex items-center gap-3">
                <select name="device_id" onchange="this.form.submit()"
                    class="rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-800 focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-600/20 shadow-xs min-w-[240px]">
                    <option value="">-- Pilih Unit Komposter --</option>
                    @foreach ($devices as $dev)
                        <option value="{{ $dev->id }}" {{ $selectedDevice && $selectedDevice->id === $dev->id ? 'selected' : '' }}>
                            {{ $dev->device_code }} — RT {{ $dev->neighborhood->rt_number ?? '-' }}/RW {{ $dev->neighborhood->rw_number ?? '-' }} ({{ $dev->current_status->label() }})
                        </option>
                    @endforeach
                </select>

                @if ($selectedDevice)
                    <a href="{{ route('admin.monitoring.index') }}" class="text-xs text-slate-500 hover:text-slate-800 underline">Reset</a>
                @endif
            </form>
        </div>
    </div>

    @if (! $selectedDevice)
        <!-- Empty State Prompt -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center shadow-xs">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-700 mx-auto flex items-center justify-center mb-4 border border-emerald-100">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <h2 class="text-base font-bold text-slate-900 mb-1">Silakan Pilih Perangkat Komposter</h2>
            <p class="text-xs text-slate-500 max-w-md mx-auto mb-5">
                Pilih unit alat melalui menu selektor di atas untuk membuka instrumen monitoring sensor suhu, display timbangan massa, dan remote control.
            </p>
            <div class="flex justify-center gap-3 text-xs">
                @foreach ($devices->take(3) as $d)
                    <a href="{{ route('admin.monitoring.index', ['device_id' => $d->id]) }}"
                        class="px-3.5 py-2 rounded-xl bg-slate-50 hover:bg-emerald-50 hover:text-emerald-800 border border-slate-200 font-semibold transition">
                        Pilih {{ $d->device_code }}
                    </a>
                @endforeach
            </div>
        </div>
    @else
        <!-- 2. SELECTED DEVICE INFORMATION STRIP -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700 shrink-0 font-bold text-xs">
                    IoT
                </div>
                <div class="min-w-0">
                    <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Kode & Model Unit</div>
                    <div class="text-sm font-bold text-slate-900 truncate">{{ $selectedDevice->device_code }}</div>
                    <div class="text-[11px] text-slate-500 truncate">{{ $selectedDevice->model_name }}</div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                    <span class="w-3.5 h-3.5 rounded-full {{ $selectedDevice->current_status === \App\Enums\DeviceStatus::RUNNING ? 'bg-blue-600 animate-ping' : ($selectedDevice->current_status === \App\Enums\DeviceStatus::IDLE ? 'bg-emerald-600' : 'bg-rose-600') }}"></span>
                </div>
                <div class="min-w-0">
                    <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Status Operasi</div>
                    <div class="text-sm font-bold text-slate-900">{{ $selectedDevice->current_status->label() }}</div>
                    <div class="text-[11px] text-slate-500">
                        Heartbeat: {{ $selectedDevice->last_heartbeat_at ? $selectedDevice->last_heartbeat_at->diffForHumans() : 'Belum Terhubung' }}
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700 shrink-0">
                    <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Kesehatan Perangkat</div>
                    <div class="text-sm font-bold text-slate-900">{{ $selectedDevice->health_status->label() }}</div>
                    <div class="text-[11px] text-slate-500">Failsafe ESP32 Aktif</div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700 shrink-0">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Lokasi Pemasangan</div>
                    <div class="text-xs font-bold text-slate-900 truncate">RT {{ $selectedDevice->neighborhood->rt_number ?? '-' }} / RW {{ $selectedDevice->neighborhood->rw_number ?? '-' }}</div>
                    <div class="text-[11px] text-slate-500 truncate">{{ $selectedDevice->neighborhood->village->name ?? '' }}, {{ $selectedDevice->city->name ?? '' }}</div>
                </div>
            </div>
        </div>

        <!-- 3. PERMISSION GATEWAY (If Admin Kota / Admin Lapangan lacks permission) -->
        @if (! $hasControlAccess)
            <div class="bg-amber-50 border border-amber-200/80 rounded-2xl p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 text-amber-900 font-bold text-sm">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span>Izin Kendali Operasional Diperlukan</span>
                        </div>
                        <p class="text-xs text-amber-800 mt-1 max-w-2xl leading-relaxed">
                            Berdasarkan protokol keamanan Smart Green Composter, {{ auth()->user()->role->label() }} wajib meminta persetujuan dari <strong>Admin RT/RW pemilik perangkat</strong> sebelum dapat menyalakan atau mengontrol mesin komposter.
                        </p>
                    </div>

                    <!-- Request Permission Form -->
                    <form method="POST" action="{{ route('admin.monitoring.request-permission', $selectedDevice->id) }}" class="flex items-center gap-2">
                        @csrf
                        <input type="hidden" name="action_type" value="run_machine">
                        <select name="duration_minutes" class="rounded-xl border border-amber-300 bg-white px-3 py-2 text-xs font-semibold text-slate-800 focus:outline-none">
                            <option value="60">Izin 60 Menit</option>
                            <option value="120">Izin 120 Menit</option>
                            <option value="240">Izin 4 Jam Penuh</option>
                        </select>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs transition shadow-xs">
                            Ajukan Izin Kontrol
                        </button>
                    </form>
                </div>
            </div>
        @else
            <!-- ACTIVE SESSION NOTICE -->
            @if ($activePermission)
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 mb-6 flex items-center justify-between text-xs text-emerald-900">
                    <div class="flex items-center gap-2 font-semibold">
                        <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Sesi Kendali Diizinkan oleh Admin RT/RW (Berlaku hingga: {{ $activePermission->expires_at->format('H:i') }} WIB)</span>
                    </div>
                    <span class="text-[11px] text-emerald-700 font-mono-numeric">Sisa {{ now()->diffInMinutes($activePermission->expires_at) }} Menit</span>
                </div>
            @endif
        @endif

        <!-- 4. INDUSTRIAL CONTROL PANEL CORE (Dual Gauge + Control Switchboard) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            
            <!-- INSTRUMENT 1: DUAL TELEMETRY GAUGES (Col-Span 2) -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 tracking-tight">Instrumen Telemetri Sensor Aktual</h2>
                        <p class="text-xs text-slate-500">Data live dari sensor DHT22 & LoadCell HX711 (ESP32)</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-xs font-semibold text-slate-700">Polling Aktif (5s)</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Gauge 1: Temperature Sensor -->
                    <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200/70 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Suhu Tabung Reaktor</span>
                            <span class="text-[11px] px-2 py-0.5 rounded-full {{ $selectedDevice->current_temperature > 65 ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }} font-bold font-mono-numeric">
                                {{ $selectedDevice->current_temperature > 65 ? 'OVERHEAT' : 'NORMAL' }}
                            </span>
                        </div>

                        @php
                            $minTemp = $selectedDevice->configuration->min_temp_limit ?? 20;
                            $maxTemp = $selectedDevice->configuration->max_temp_limit ?? 65;
                            $cutoffTemp = $selectedDevice->configuration->critical_temp_cutoff ?? 70;
                        @endphp
                        <div class="my-6 text-center">
                            <div class="text-5xl font-black text-slate-900 font-mono-numeric tracking-tight">
                                {{ number_format($selectedDevice->current_temperature, 1) }}<span class="text-2xl text-slate-400 font-medium ml-1">°C</span>
                            </div>
                            <div class="text-xs text-slate-500 mt-2">Batas Suhu: {{ number_format($minTemp, 0) }}°C s/d {{ number_format($maxTemp, 0) }}°C</div>
                        </div>

                        <!-- Progress Bar Arc -->
                        <div>
                            @php
                                $tempPct = min(100, max(0, ($selectedDevice->current_temperature / 70) * 100));
                            @endphp
                            <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden p-0.5">
                                <div class="h-2 rounded-full transition-all duration-500 {{ $selectedDevice->current_temperature > $maxTemp ? 'bg-rose-600' : ($selectedDevice->current_temperature > 50 ? 'bg-amber-500' : 'bg-emerald-600') }}"
                                    style="width: {{ $tempPct }}%"></div>
                            </div>
                            <div class="flex justify-between text-[10px] text-slate-400 font-mono-numeric mt-1.5">
                                <span>Min: {{ number_format($minTemp, 0) }}°C</span>
                                <span>Max: {{ number_format($maxTemp, 0) }}°C</span>
                                <span>Cutoff: {{ number_format($cutoffTemp, 0) }}°C</span>
                            </div>
                        </div>
                    </div>

                    <!-- Gauge 2: Weight Sensor (LoadCell HX711) -->
                    <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200/70 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Timbangan Massa Sampah</span>
                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 font-bold font-mono-numeric">
                                HX711 CALIBRATED
                            </span>
                        </div>

                        <div class="my-6 text-center">
                            <div class="text-5xl font-black text-slate-900 font-mono-numeric tracking-tight">
                                {{ number_format($selectedDevice->current_weight, 1) }}<span class="text-2xl text-slate-400 font-medium ml-1">Kg</span>
                            </div>
                            <div class="text-xs text-slate-500 mt-2">Kapasitas Maksimal Tabung: 50.0 Kg</div>
                        </div>

                        <!-- Progress Bar Arc -->
                        <div>
                            @php
                                $weightPct = min(100, max(0, ($selectedDevice->current_weight / 50) * 100));
                            @endphp
                            <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden p-0.5">
                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" style="width: {{ $weightPct }}%"></div>
                            </div>
                            <div class="flex justify-between text-[10px] text-slate-400 font-mono-numeric mt-1.5">
                                <span>0 Kg (Kosong)</span>
                                <span>Terisi {{ round($weightPct) }}%</span>
                                <span>Max 50 Kg</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Safety Guidelines Banner -->
                <div class="mt-6 p-4 rounded-xl bg-slate-50 border border-slate-200/60 text-xs text-slate-600 flex items-start gap-3">
                    <svg class="w-5 h-5 text-emerald-700 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="leading-relaxed">
                        <strong>Prosedur Keselamatan Industri:</strong> ESP32 menjalankan timer lokal dan proteksi suhu fisik secara otonom. Jika suhu mencapai <strong>70°C</strong> atau sensor disconnect, mesin akan melakukan pemadaman darurat seketika tanpa bergantung pada koneksi internet.
                    </div>
                </div>
            </div>

            <!-- INSTRUMENT 2: PHYSICAL CONTROL CONSOLE (Col-Span 1) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="pb-4 mb-4 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900 tracking-tight">Konsol Kendali Mesin</h2>
                        <p class="text-xs text-slate-500">Antarmuka eksekusi perintah motor & timer</p>
                    </div>

                    @if ($hasControlAccess)
                        <div class="space-y-4">
                            <!-- Power Action Buttons -->
                            <div class="grid grid-cols-2 gap-3">
                                <!-- Power ON Form -->
                                <form method="POST" action="{{ route('admin.monitoring.send-command', $selectedDevice->id) }}"
                                    onsubmit="return confirm('Konfirmasi: Nyalakan motor pengolah sampah untuk perangkat {{ $selectedDevice->device_code }}? Sensor timbangan awal akan dicatat.');">
                                    @csrf
                                    <input type="hidden" name="command_type" value="POWER_ON">
                                    <input type="hidden" name="timer_minutes" value="240">
                                    <button type="submit"
                                        class="w-full py-3.5 px-4 rounded-xl font-bold text-xs text-white bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] transition flex flex-col items-center justify-center gap-1 shadow-xs {{ $selectedDevice->current_status === \App\Enums\DeviceStatus::RUNNING ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        {{ $selectedDevice->current_status === \App\Enums\DeviceStatus::RUNNING ? 'disabled' : '' }}>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>POWER ON</span>
                                    </button>
                                </form>

                                <!-- Power OFF Form -->
                                <form method="POST" action="{{ route('admin.monitoring.send-command', $selectedDevice->id) }}"
                                    onsubmit="return confirm('Hentikan operasi mesin secara normal?');">
                                    @csrf
                                    <input type="hidden" name="command_type" value="POWER_OFF">
                                    <button type="submit"
                                        class="w-full py-3.5 px-4 rounded-xl font-bold text-xs text-slate-800 bg-slate-100 hover:bg-slate-200 active:scale-[0.98] transition flex flex-col items-center justify-center gap-1 border border-slate-200">
                                        <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" />
                                        </svg>
                                        <span>POWER OFF</span>
                                    </button>
                                </form>
                            </div>

                            <!-- Timer Preset & Custom Hours Selector Form -->
                            <form method="POST" action="{{ route('admin.monitoring.send-command', $selectedDevice->id) }}" class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-2.5">
                                @csrf
                                <input type="hidden" name="command_type" value="SET_TIMER">
                                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">Atur Durasi Komposter (Jam)</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <button type="submit" name="timer_minutes" value="120" class="py-1.5 px-2 rounded-lg bg-white border border-slate-200 hover:border-emerald-600 text-[11px] font-semibold text-slate-800 transition">2 Jam</button>
                                    <button type="submit" name="timer_minutes" value="180" class="py-1.5 px-2 rounded-lg bg-white border border-slate-200 hover:border-emerald-600 text-[11px] font-semibold text-slate-800 transition">3 Jam</button>
                                    <button type="submit" name="timer_minutes" value="240" class="py-1.5 px-2 rounded-lg bg-emerald-50 border border-emerald-300 text-[11px] font-bold text-emerald-800 transition">4 Jam</button>
                                </div>
                                <div class="flex items-center gap-2 pt-1">
                                    <input type="number" name="timer_hours" min="1" max="24" placeholder="Jam (1-24)"
                                        class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs text-slate-900 font-mono-numeric focus:outline-none focus:border-emerald-600">
                                    <button type="submit" class="shrink-0 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-900 text-white text-[11px] font-semibold transition">
                                        Set Jam
                                    </button>
                                </div>
                            </form>

                            <!-- Temperature Upper & Lower Threshold Form -->
                            <form method="POST" action="{{ route('admin.monitoring.send-command', $selectedDevice->id) }}" class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-2.5">
                                @csrf
                                <input type="hidden" name="command_type" value="SET_TEMP_LIMIT">
                                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">Atur Batas Suhu (°C)</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <span class="block text-[10px] text-slate-500 mb-1">Batas Bawah (°C)</span>
                                        <input type="number" step="0.5" min="10" max="45" name="min_temp"
                                            value="{{ number_format($selectedDevice->configuration->min_temp_limit ?? 20, 1, '.', '') }}" required
                                            class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs text-slate-900 font-mono-numeric focus:outline-none focus:border-emerald-600">
                                    </div>
                                    <div>
                                        <span class="block text-[10px] text-slate-500 mb-1">Batas Atas (°C)</span>
                                        <input type="number" step="0.5" min="40" max="70" name="max_temp"
                                            value="{{ number_format($selectedDevice->configuration->max_temp_limit ?? 65, 1, '.', '') }}" required
                                            class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs text-slate-900 font-mono-numeric focus:outline-none focus:border-emerald-600">
                                    </div>
                                </div>
                                <button type="submit" class="w-full py-1.5 px-3 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-semibold transition">
                                    Simpan Batas Suhu
                                </button>
                            </form>

                            <!-- Emergency Stop Button -->
                            <form method="POST" action="{{ route('admin.monitoring.send-command', $selectedDevice->id) }}"
                                onsubmit="return confirm('PERINGATAN: EMERGENCY STOP akan langsung mematikan relay motor dan pemanas secara paksa. Lanjutkan?');">
                                @csrf
                                <input type="hidden" name="command_type" value="EMERGENCY_STOP">
                                <button type="submit"
                                    class="w-full py-2.5 px-4 rounded-xl font-bold text-xs text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-300 transition flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                    <span>EMERGENCY STOP (Darurat)</span>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="p-6 rounded-xl bg-slate-50 border border-slate-200/80 text-center text-xs text-slate-500">
                            <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Tombol kendali dinonaktifkan sampai pengajuan izin disetujui oleh Admin RT/RW.
                        </div>
                    @endif
                </div>

                <!-- Operator In Charge Info -->
                <div class="mt-6 pt-4 border-t border-slate-100 text-[11px] text-slate-500 flex items-center justify-between">
                    <span>Pengendali Aktif:</span>
                    <span class="font-semibold text-slate-900">{{ auth()->user()->name }}</span>
                </div>
            </div>
        </div>

        <!-- 5. COMMAND QUEUE & RECENT PROCESS SESSIONS -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Command History -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <h3 class="text-sm font-bold text-slate-900 mb-1">Antrean Perintah Perangkat (Queue & ACK)</h3>
                <p class="text-xs text-slate-500 mb-3">Instruksi kontrol yang dikirimkan ke mikrokontroler ESP32</p>

                <div class="divide-y divide-slate-100 text-xs">
                    @forelse ($recentCommands as $cmd)
                        <div class="py-2.5 flex items-center justify-between">
                            <div>
                                <span class="font-bold text-slate-800">{{ $cmd->command_type }}</span>
                                <span class="text-[10px] text-slate-400 ml-2 font-mono-numeric">{{ $cmd->created_at->format('H:i:s') }}</span>
                                <div class="text-[11px] text-slate-500">Oleh: {{ $cmd->issuer->name ?? 'System' }}</div>
                            </div>
                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-semibold {{ $cmd->status === 'executed' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                                {{ strtoupper($cmd->status) }}
                            </span>
                        </div>
                    @empty
                        <div class="py-6 text-center text-slate-400">Belum ada perintah yang dikirim.</div>
                    @endforelse
                </div>
            </div>

            <!-- Historical Session of this device -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <h3 class="text-sm font-bold text-slate-900 mb-1">Riwayat Pengolahan Perangkat Ini</h3>
                <p class="text-xs text-slate-500 mb-3">Pencatatan berat awal dan akhir sampah</p>

                <div class="divide-y divide-slate-100 text-xs">
                    @forelse ($recentSessions as $sess)
                        <div class="py-2.5 flex items-center justify-between">
                            <div>
                                <div class="font-bold text-slate-900 font-mono-numeric">Awal: {{ number_format($sess->initial_weight, 1) }} Kg → Akhir: {{ number_format($sess->final_weight, 1) }} Kg</div>
                                <div class="text-[11px] text-slate-500">{{ $sess->started_at->translatedFormat('d M Y, H:i') }} • Oleh: {{ $sess->operator->name ?? 'Petugas' }}</div>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-emerald-700 font-mono-numeric">-{{ number_format($sess->weight_reduction_kg, 1) }} Kg</span>
                                <div class="text-[10px] text-slate-500">({{ $sess->reduction_percentage }}% Reduksi)</div>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-slate-400">Belum ada riwayat proses pada unit ini.</div>
                    @endforelse
                </div>
            </div>
        </div>

        @if ($user->isAdminLapangan())
            <!-- 6. ADMIN LAPANGAN SPECIFIC: ASSIGNED DUTY SCHEDULES & PERSONAL HISTORY (Section 5.4) -->
            <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Today's Duty Schedule -->
                <div class="bg-white rounded-2xl border border-emerald-200/80 p-5 shadow-xs bg-emerald-50/20">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <h3 class="text-sm font-bold text-slate-900">Jadwal Tugas Piket Saya Hari Ini</h3>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">
                            {{ now()->translatedFormat('l, d F Y') }}
                        </span>
                    </div>

                    <div class="divide-y divide-emerald-100 text-xs">
                        @forelse ($todaySchedules as $sched)
                            <div class="py-3 flex items-center justify-between">
                                <div>
                                    <span class="font-bold font-mono-numeric text-slate-900">{{ substr($sched->start_time, 0, 5) }} - {{ substr($sched->end_time, 0, 5) }} WIB</span>
                                    <p class="text-slate-600 mt-0.5">{{ $sched->notes ?? 'Piket operasional dan penimbangan sampah rutin.' }}</p>
                                </div>
                                <span class="inline-flex px-2 py-1 rounded text-[10px] font-bold bg-white text-emerald-800 border border-emerald-200">
                                    Tugas Aktif
                                </span>
                            </div>
                        @empty
                            <div class="py-6 text-center text-slate-500">
                                Tidak ada jadwal piket penugasan khusus untuk hari ini.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Personal Operation History -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-bold text-slate-900">Riwayat Pengoperasian Pribadi Saya</h3>
                        <span class="text-[10px] font-semibold text-slate-400">5 Sesi Terakhir</span>
                    </div>

                    <div class="divide-y divide-slate-100 text-xs">
                        @forelse ($myRecentSessions as $mySess)
                            <div class="py-2.5 flex items-center justify-between">
                                <div>
                                    <div class="font-bold text-slate-900 font-mono-numeric">
                                        {{ $mySess->device->device_code ?? '-' }} • Bobot Awal: {{ number_format($mySess->initial_weight, 1) }} Kg
                                    </div>
                                    <div class="text-[10px] text-slate-500">
                                        {{ $mySess->started_at ? $mySess->started_at->translatedFormat('d M Y, H:i') : '-' }} &bull; Durasi: {{ $mySess->actual_duration_minutes ?? 240 }} mnt
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-emerald-700 font-mono-numeric block">
                                        -{{ number_format($mySess->weight_reduction_kg, 1) }} Kg
                                    </span>
                                    <span class="text-[10px] text-slate-500">
                                        ({{ number_format($mySess->reduction_percentage, 1) }}% Reduksi)
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="py-6 text-center text-slate-400">
                                Anda belum memiliki riwayat pengoperasian mesin yang tercatat.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif
    @endif

</x-admin-layout>
