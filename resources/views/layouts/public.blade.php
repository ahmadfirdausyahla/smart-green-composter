<!DOCTYPE html>
<html lang="id" class="h-full bg-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Smart Green Composter' }} — ZeroWaste Green</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Leaflet CSS for GIS Mapping -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .font-mono-numeric {
            font-family: 'JetBrains Mono', monospace;
        }
    </style>
</head>
<body class="min-h-full flex flex-col text-slate-800 antialiased selection:bg-emerald-500 selection:text-white">

    <!-- TOP HEADER / NAVBAR -->
    <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand -->
            <a href="{{ route('public.home') }}" class="flex items-center gap-2.5 group">
                <div class="w-9 h-9 rounded-xl bg-emerald-600 flex items-center justify-center text-white shadow-xs group-hover:bg-emerald-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
                <div>
                    <span class="block text-sm font-bold text-slate-900 leading-tight">Smart Green Composter</span>
                    <span class="text-[10px] font-semibold text-emerald-700 tracking-wider">ZERO WASTE GREEN</span>
                </div>
            </a>

            <!-- Nav Links -->
            <nav class="hidden md:flex items-center gap-6 text-xs font-semibold text-slate-600">
                <a href="{{ route('public.home') }}" class="hover:text-emerald-700 transition {{ request()->routeIs('public.home') ? 'text-emerald-700 font-bold' : '' }}">Beranda</a>
                <a href="{{ route('public.gis') }}" class="hover:text-emerald-700 transition {{ request()->routeIs('public.gis') ? 'text-emerald-700 font-bold' : '' }}">Persebaran Mesin GIS</a>
                <a href="{{ route('public.stats') }}" class="hover:text-emerald-700 transition {{ request()->routeIs('public.stats') ? 'text-emerald-700 font-bold' : '' }}">Statistik Dampak</a>
                <a href="#cara-kerja" class="hover:text-emerald-700 transition">Cara Kerja</a>
            </nav>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('admin.monitoring.index') }}"
                        class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs transition">
                        Buka Dashboard Admin
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold transition border border-slate-200">
                        Masuk Admin
                    </a>
                    <a href="{{ route('register.rtrw') }}"
                        class="hidden sm:inline-flex px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs transition">
                        Daftar RT/RW
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- MAIN BODY -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    <!-- FOOTER -->
    <footer class="bg-slate-50 border-t border-slate-200/80 py-12 text-slate-600 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="md:col-span-2">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 rounded-lg bg-emerald-600 flex items-center justify-center text-white text-xs font-bold">
                        Z
                    </div>
                    <span class="font-bold text-slate-900 text-sm">Smart Green Composter</span>
                </div>
                <p class="text-slate-500 leading-relaxed max-w-sm mb-4 text-xs">
                    Inisiatif pengelolaan sampah organik berbasis IoT dan mikrokontroler ESP32 terdesentralisasi di lingkungan RT/RW untuk mewujudkan Indonesia Zero Waste.
                </p>
                <div class="text-[11px] text-slate-400">
                    Dikembangkan oleh Tim <strong>ZeroWaste Green</strong>.
                </div>
            </div>

            <div>
                <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider mb-3">Tautan Publik</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('public.home') }}" class="hover:text-emerald-700 transition">Beranda</a></li>
                    <li><a href="{{ route('public.gis') }}" class="hover:text-emerald-700 transition">Peta Persebaran GIS</a></li>
                    <li><a href="{{ route('public.stats') }}" class="hover:text-emerald-700 transition">Statistik Reduksi Massa</a></li>
                    <li><a href="{{ route('register.rtrw') }}" class="hover:text-emerald-700 transition">Pendaftaran Pengurus RT/RW</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider mb-3">Akses Administrator</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('login') }}" class="hover:text-emerald-700 transition">Login Master Admin</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-emerald-700 transition">Login Admin Kota/Kabupaten</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-emerald-700 transition">Login Admin Lingkungan RT/RW</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-emerald-700 transition">Login Petugas Lapangan</a></li>
                </ul>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 pt-6 border-t border-slate-200/60 text-center text-slate-400 text-[11px]">
            &copy; {{ date('Y') }} ZeroWaste Green — Smart Green Composter Platform. Hak Cipta Dilindungi.
        </div>
    </footer>

    <!-- 5. FLOATING AI CHATBOT WIDGET (RAG Powered) -->
    <div id="chatbot-container" class="fixed bottom-5 right-5 z-50">
        <!-- Chat Panel (Hidden by default) -->
        <div id="chat-panel" class="hidden mb-3 w-80 sm:w-96 bg-white rounded-2xl border border-slate-200 shadow-xl overflow-hidden flex flex-col h-[460px] animate-in fade-in slide-in-from-bottom-2 duration-200">
            <!-- Chat Header -->
            <div class="bg-emerald-600 p-3.5 text-white flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center font-bold text-xs">
                        AI
                    </div>
                    <div>
                        <div class="font-bold text-xs leading-tight">Asisten ZeroWaste Green</div>
                        <div class="text-[10px] text-emerald-100">Informasi Sampah & Komposter RAG</div>
                    </div>
                </div>
                <button onclick="toggleChat()" class="text-white/80 hover:text-white p-1 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Messages Log -->
            <div id="chat-messages" class="flex-1 p-3.5 overflow-y-auto space-y-3 text-xs">
                <div class="flex items-start gap-2">
                    <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold flex items-center justify-center shrink-0">
                        AI
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-100 text-slate-800 max-w-[85%] leading-relaxed">
                        Halo! Saya asisten informasi <strong>Smart Green Composter</strong>. Anda dapat bertanya seputar jenis sampah yang dapat diolah, cara kerja mesin, atau prosedur keselamatan.
                    </div>
                </div>
            </div>

            <!-- Typing Indicator -->
            <div id="chat-typing" class="hidden px-4 py-1 text-[11px] text-slate-400 italic">
                AI sedang mencari jawaban di Knowledge Base...
            </div>

            <!-- Quick Suggestions -->
            <div class="px-3 py-1.5 bg-slate-50 border-t border-slate-100 flex items-center gap-1.5 overflow-x-auto text-[10px]">
                <button onclick="askPreset('Sampah apa saja yang boleh diolah?')" class="px-2 py-1 rounded bg-white border border-slate-200 text-slate-600 hover:border-emerald-500 whitespace-nowrap">
                    Sampah yang boleh?
                </button>
                <button onclick="askPreset('Bagaimana cara kerja mesin?')" class="px-2 py-1 rounded bg-white border border-slate-200 text-slate-600 hover:border-emerald-500 whitespace-nowrap">
                    Cara kerja mesin?
                </button>
                <button onclick="askPreset('Berapa suhu maksimal aman?')" class="px-2 py-1 rounded bg-white border border-slate-200 text-slate-600 hover:border-emerald-500 whitespace-nowrap">
                    Suhu aman?
                </button>
            </div>

            <!-- Chat Input -->
            <form onsubmit="handleSendChat(event)" class="p-2.5 bg-white border-t border-slate-100 flex items-center gap-2">
                <input id="chat-input" type="text" placeholder="Tanyakan seputar komposter..." required
                    class="flex-1 rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-800 focus:outline-none focus:border-emerald-600">
                <button type="submit" class="p-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white shrink-0 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </form>
        </div>

        <!-- Floating Toggle Trigger Button -->
        <button onclick="toggleChat()"
            class="ml-auto w-12 h-12 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg flex items-center justify-center transition active:scale-95 group">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>
        </button>
    </div>

    <!-- AI Chatbot Client Script -->
    <script>
        function toggleChat() {
            const panel = document.getElementById('chat-panel');
            panel.classList.toggle('hidden');
            if (!panel.classList.contains('hidden')) {
                document.getElementById('chat-input').focus();
            }
        }

        function askPreset(text) {
            document.getElementById('chat-input').value = text;
            handleSendChat(new Event('submit'));
        }

        async function handleSendChat(e) {
            e.preventDefault();
            const input = document.getElementById('chat-input');
            const message = input.value.trim();
            if (!message) return;

            const messagesContainer = document.getElementById('chat-messages');
            const typingIndicator = document.getElementById('chat-typing');

            // Render User Message
            messagesContainer.innerHTML += `
                <div class="flex items-start justify-end gap-2">
                    <div class="p-2.5 rounded-xl bg-emerald-600 text-white max-w-[85%] leading-relaxed text-xs">
                        ${escapeHtml(message)}
                    </div>
                </div>
            `;
            input.value = '';
            messagesContainer.scrollTop = messagesContainer.scrollHeight;

            typingIndicator.classList.remove('hidden');

            try {
                const response = await fetch("{{ route('public.chatbot') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ message: message })
                });

                const data = await response.json();
                typingIndicator.classList.add('hidden');

                const replyHtml = escapeHtml(data.reply).replace(/\n/g, '<br>');
                const sourceBadge = data.source ? `<div class="mt-1 pt-1 border-t border-slate-200 text-[9px] text-slate-500 font-medium">Sumber: ${escapeHtml(data.source)}</div>` : '';

                messagesContainer.innerHTML += `
                    <div class="flex items-start gap-2">
                        <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold flex items-center justify-center shrink-0">
                            AI
                        </div>
                        <div class="p-2.5 rounded-xl bg-slate-100 text-slate-800 max-w-[85%] leading-relaxed text-xs">
                            ${replyHtml}
                            ${sourceBadge}
                        </div>
                    </div>
                `;
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            } catch (err) {
                typingIndicator.classList.add('hidden');
                messagesContainer.innerHTML += `
                    <div class="flex items-start gap-2">
                        <div class="w-6 h-6 rounded-full bg-rose-100 text-rose-800 text-[10px] font-bold flex items-center justify-center shrink-0">
                            !
                        </div>
                        <div class="p-2 rounded-xl bg-rose-50 text-rose-700 text-xs">
                            Gagal menghubungi asisten AI. Silakan coba kembali nanti.
                        </div>
                    </div>
                `;
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.innerText = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>
