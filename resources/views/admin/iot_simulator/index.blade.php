<x-admin-layout title="Simulator IoT ESP32" headerTitle="Simulator Mikrokontroler IoT ESP32" headerSubtitle="Alat pengujian integrasi telemetri sensor, kendali relay, dan simulasi kondisi darurat (Development)">

    <!-- Simulation Watermark Warning Banner -->
    <div class="mb-6 rounded-2xl bg-amber-50 border border-amber-300 p-4 text-xs text-amber-900 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="w-3 h-3 rounded-full bg-amber-500 animate-ping"></span>
            <div>
                <strong class="font-bold text-amber-950 uppercase tracking-wider">Mode Simulator IoT (Pengujian Pengembangan):</strong>
                <span> Halaman ini mensimulasikan mikroprosesor ESP32 dengan mengirim paket HTTP JSON nyata ke endpoint <code>/api/v1/devices/telemetry</code> dan <code>/api/v1/devices/commands/ack</code>.</span>
            </div>
        </div>
        <span class="px-2.5 py-1 rounded-lg bg-amber-200/80 text-amber-900 font-bold text-[10px] tracking-wider uppercase">
            Dev Simulator
        </span>
    </div>

    <!-- 1. SELECT DEVICE TO SIMULATE -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Pilih Perangkat untuk Disimulasikan</h3>
                <p class="text-xs text-slate-500">Gunakan API Key yang terdaftar untuk otentikasi header HTTP ESP32</p>
            </div>

            <form method="GET" action="{{ route('admin.iot-simulator.index') }}">
                <select name="device_id" onchange="this.form.submit()"
                    class="rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-800 focus:outline-none focus:border-emerald-600">
                    @foreach ($devices as $d)
                        <option value="{{ $d->id }}" {{ $selectedDevice && $selectedDevice->id === $d->id ? 'selected' : '' }}>
                            {{ $d->device_code }} — {{ $d->model_name }} ({{ $d->city->name ?? '' }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        @if ($selectedDevice)
            <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div>
                    <span class="text-slate-400 text-[10px] block">Kode Mesin</span>
                    <span class="font-mono-numeric font-bold text-slate-900">{{ $selectedDevice->device_code }}</span>
                </div>
                <div>
                    <span class="text-slate-400 text-[10px] block">Status Saat Ini</span>
                    <span class="font-bold text-emerald-700">{{ $selectedDevice->current_status->label() }}</span>
                </div>
                <div>
                    <span class="text-slate-400 text-[10px] block">Batas Suhu Maks</span>
                    <span class="font-mono-numeric font-bold text-slate-900">{{ $selectedDevice->configuration->max_temp_limit ?? 65 }} °C</span>
                </div>
                <div>
                    <span class="text-slate-400 text-[10px] block">Batas Cutoff Kritis</span>
                    <span class="font-mono-numeric font-bold text-rose-700">{{ $selectedDevice->configuration->critical_temp_cutoff ?? 70 }} °C</span>
                </div>
            </div>
        @endif
    </div>

    @if ($selectedDevice)
        <!-- 2. SIMULATION CONTROLS & TEST TRANSMITTER -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Left: Simulated Sensors Setup -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                        <h3 class="text-sm font-bold text-slate-900">Virtual Sensor Sliders</h3>
                        <span class="text-[11px] text-slate-400 font-mono-numeric">ESP32 Firmware Mock</span>
                    </div>

                    <!-- Temperature Slider -->
                    <div class="mb-5">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-bold text-slate-700">Simulasi Suhu Tabung (°C)</label>
                            <span id="tempValDisplay" class="font-mono-numeric font-bold text-emerald-700 text-sm">45.0 °C</span>
                        </div>
                        <input id="tempSlider" type="range" min="15" max="85" step="0.5" value="45.0"
                            oninput="updateDisplays()"
                            class="w-full accent-emerald-600 cursor-pointer">
                        <div class="flex justify-between text-[10px] text-slate-400 font-mono-numeric mt-1">
                            <span>15°C (Dingin)</span>
                            <span>45°C (Ideal)</span>
                            <span>65°C (Warning)</span>
                            <span>70°C+ (Cutoff)</span>
                        </div>
                    </div>

                    <!-- Weight Slider -->
                    <div class="mb-5">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-bold text-slate-700">Simulasi Timbangan LoadCell (Kg)</label>
                            <span id="weightValDisplay" class="font-mono-numeric font-bold text-blue-700 text-sm">24.5 Kg</span>
                        </div>
                        <input id="weightSlider" type="range" min="0" max="50" step="0.1" value="24.5"
                            oninput="updateDisplays()"
                            class="w-full accent-blue-600 cursor-pointer">
                        <div class="flex justify-between text-[10px] text-slate-400 font-mono-numeric mt-1">
                            <span>0 Kg (Kosong)</span>
                            <span>25 Kg (Separuh)</span>
                            <span>50 Kg (Kapasitas Penuh)</span>
                        </div>
                    </div>

                    <!-- Motor & Heater Toggle States -->
                    <div class="grid grid-cols-2 gap-3 mb-5 text-xs">
                        <label class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center gap-2 cursor-pointer">
                            <input id="motorToggle" type="checkbox" checked class="h-4 w-4 rounded text-emerald-600">
                            <span class="font-bold text-slate-800">Motor Pencacah ON</span>
                        </label>
                        <label class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center gap-2 cursor-pointer">
                            <input id="heaterToggle" type="checkbox" checked class="h-4 w-4 rounded text-emerald-600">
                            <span class="font-bold text-slate-800">Elemen Pemanas ON</span>
                        </label>
                    </div>

                    <!-- Quick Preset Scenarios -->
                    <div class="pt-3 border-t border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Preset Uji Coba Cepat:</span>
                        <div class="flex flex-wrap gap-2 text-[11px]">
                            <button type="button" onclick="setPreset(42.0, 25.0, true, true)"
                                class="px-2.5 py-1.5 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 font-semibold hover:bg-emerald-100">
                                🟢 Normal (42°C)
                            </button>
                            <button type="button" onclick="setPreset(66.5, 30.0, true, true)"
                                class="px-2.5 py-1.5 rounded-lg bg-amber-50 text-amber-800 border border-amber-200 font-semibold hover:bg-amber-100">
                                🟡 Warning Overheat (66.5°C)
                            </button>
                            <button type="button" onclick="setPreset(73.0, 30.0, false, false)"
                                class="px-2.5 py-1.5 rounded-lg bg-rose-50 text-rose-800 border border-rose-200 font-semibold hover:bg-rose-100">
                                🔴 Emergency Trip (73°C Cutoff)
                            </button>
                            <button type="button" onclick="setPreset(30.0, 0.0, false, false)"
                                class="px-2.5 py-1.5 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 font-semibold hover:bg-slate-200">
                                ⚪ Standby / Kosong (0 Kg)
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-6 pt-4 border-t border-slate-100">
                    <button type="button" onclick="transmitTelemetry()"
                        class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-[0.99] text-white font-bold text-xs shadow-xs transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span>KIRIM TELEMETRI ESP32 SEKARANG (POST /api/v1/devices/telemetry)</span>
                    </button>
                </div>
            </div>

            <!-- Right: Live Transmission Log & Pending Command Receiver -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                        <h3 class="text-sm font-bold text-slate-900">Respon Server & Antrean Perintah Masuk</h3>
                        <span id="liveStatusPill" class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600">
                            STANDBY
                        </span>
                    </div>

                    <!-- Pending Command Catch Alert -->
                    <div id="pendingCommandContainer" class="hidden mb-4 p-4 rounded-xl bg-blue-50 border border-blue-200 text-xs">
                        <div class="flex items-center justify-between font-bold text-blue-900 mb-1">
                            <span class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-blue-600 animate-ping"></span>
                                PERINTAH DITERIMA DARI LARAVEL!
                            </span>
                            <span id="cmdIdBadge" class="font-mono-numeric text-[10px] px-1.5 py-0.5 rounded bg-blue-200/70"></span>
                        </div>
                        <div class="text-[11px] text-blue-800 leading-relaxed mb-3">
                            Tipe Perintah: <strong id="cmdTypeDisplay"></strong><br>
                            Parameter: <code id="cmdParamsDisplay" class="font-mono-numeric"></code>
                        </div>
                        <button type="button" onclick="sendAckCommand()"
                            class="py-1.5 px-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-[11px] transition shadow-xs">
                            Kirim Konfirmasi ACK (Executed) ke Laravel
                        </button>
                    </div>

                    <!-- Raw JSON Payload Terminal Display -->
                    <div class="rounded-xl bg-slate-900 p-4 text-emerald-400 font-mono-numeric text-xs h-72 overflow-y-auto" id="logTerminal">
                        <div class="text-slate-500">// Terminal Log Transmisi Paket ESP32</div>
                        <div class="text-slate-500">// Device Key: {{ substr($selectedDevice->api_key, 0, 8) }}...</div>
                        <div class="text-slate-500">// Tekan tombol 'Kirim Telemetri' di sebelah kiri...</div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] text-slate-500 flex items-center justify-between">
                    <span>Endpoint: <code>/api/v1/devices/telemetry</code></span>
                    <span>Protokol: HTTP POST JSON</span>
                </div>
            </div>
        </div>
    @endif

    <script>
        const deviceApiKey = "{{ $selectedDevice->api_key ?? '' }}";
        let lastReceivedCommandId = null;

        function updateDisplays() {
            const temp = parseFloat(document.getElementById('tempSlider').value).toFixed(1);
            const weight = parseFloat(document.getElementById('weightSlider').value).toFixed(1);
            document.getElementById('tempValDisplay').innerText = temp + ' °C';
            document.getElementById('weightValDisplay').innerText = weight + ' Kg';
        }

        function setPreset(temp, weight, motor, heater) {
            document.getElementById('tempSlider').value = temp;
            document.getElementById('weightSlider').value = weight;
            document.getElementById('motorToggle').checked = motor;
            document.getElementById('heaterToggle').checked = heater;
            updateDisplays();
        }

        async function transmitTelemetry() {
            const terminal = document.getElementById('logTerminal');
            const pill = document.getElementById('liveStatusPill');

            const payload = {
                temperature: parseFloat(document.getElementById('tempSlider').value),
                weight: parseFloat(document.getElementById('weightSlider').value),
                motor_state: document.getElementById('motorToggle').checked,
                heater_state: document.getElementById('heaterToggle').checked,
                timer_remaining_sec: 7200,
                sensor_status: {
                    dht22: "OK",
                    load_cell: "OK",
                    relay: "OK"
                }
            };

            pill.className = "inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 animate-pulse";
            pill.innerText = "TRANSMITTING...";

            const timestamp = new Date().toLocaleTimeString();
            terminal.innerHTML += `\n<span class="text-slate-400">[${timestamp}] >>> Mengirim telemetry ke Laravel...</span>\n` + JSON.stringify(payload, null, 2);
            terminal.scrollTop = terminal.scrollHeight;

            try {
                const res = await fetch("{{ route('api.devices.telemetry') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Device-Key': deviceApiKey
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                pill.className = "inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700";
                pill.innerText = "200 OK SUCCESS";

                terminal.innerHTML += `\n<span class="text-emerald-300">[${timestamp}] <<< Respon Laravel:</span>\n` + JSON.stringify(data, null, 2);
                terminal.scrollTop = terminal.scrollHeight;

                // Check pending command
                if (data.pending_command) {
                    const cmdBox = document.getElementById('pendingCommandContainer');
                    cmdBox.classList.remove('hidden');
                    lastReceivedCommandId = data.pending_command.command_id;
                    document.getElementById('cmdIdBadge').innerText = 'CMD #' + data.pending_command.command_id;
                    document.getElementById('cmdTypeDisplay').innerText = data.pending_command.type;
                    document.getElementById('cmdParamsDisplay').innerText = JSON.stringify(data.pending_command.parameters);
                } else {
                    document.getElementById('pendingCommandContainer').classList.add('hidden');
                }
            } catch (err) {
                pill.className = "inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700";
                pill.innerText = "FAILED";
                terminal.innerHTML += `\n<span class="text-rose-400">[${timestamp}] Gagal mengirim request: ${err}</span>`;
            }
        }

        async function sendAckCommand() {
            if (!lastReceivedCommandId) return;

            const terminal = document.getElementById('logTerminal');
            const timestamp = new Date().toLocaleTimeString();

            const ackPayload = {
                command_id: lastReceivedCommandId,
                status: 'executed',
                details: 'Simulator ESP32 sukses menjalankan instruksi.'
            };

            try {
                const res = await fetch("{{ route('api.devices.commands.ack') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Device-Key': deviceApiKey
                    },
                    body: JSON.stringify(ackPayload)
                });

                const data = await res.json();
                terminal.innerHTML += `\n<span class="text-blue-300">[${timestamp}] <<< ACK Terkirim & Diterima Server:</span>\n` + JSON.stringify(data, null, 2);
                terminal.scrollTop = terminal.scrollHeight;
                document.getElementById('pendingCommandContainer').classList.add('hidden');
                lastReceivedCommandId = null;
            } catch (err) {
                terminal.innerHTML += `\n<span class="text-rose-400">[${timestamp}] Gagal mengirim ACK: ${err}</span>`;
            }
        }
    </script>

</x-admin-layout>
