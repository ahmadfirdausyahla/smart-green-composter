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
<body class="min-h-full flex flex-col text-slate-800 antialiased selection:bg-emerald-500 selection:text-white" x-data="{ mobileMenu: false }">

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

            <!-- Desktop Nav Links -->
            <nav class="hidden lg:flex items-center gap-5 text-xs font-semibold text-slate-600">
                <a href="{{ route('public.home') }}" class="hover:text-emerald-700 transition {{ request()->routeIs('public.home') ? 'text-emerald-700 font-bold' : '' }}">Beranda</a>
                <a href="{{ route('public.about') }}" class="hover:text-emerald-700 transition {{ request()->routeIs('public.about') ? 'text-emerald-700 font-bold' : '' }}">Tentang</a>
                <a href="{{ route('public.how-it-works') }}" class="hover:text-emerald-700 transition {{ request()->routeIs('public.how-it-works') ? 'text-emerald-700 font-bold' : '' }}">Cara Kerja</a>
                <a href="{{ route('public.technology') }}" class="hover:text-emerald-700 transition {{ request()->routeIs('public.technology') ? 'text-emerald-700 font-bold' : '' }}">Teknologi</a>
                <a href="{{ route('public.gis') }}" class="hover:text-emerald-700 transition {{ request()->routeIs('public.gis') ? 'text-emerald-700 font-bold' : '' }}">Peta GIS</a>
                <a href="{{ route('public.stats') }}" class="hover:text-emerald-700 transition {{ request()->routeIs('public.stats') ? 'text-emerald-700 font-bold' : '' }}">Statistik</a>
                <a href="{{ route('public.news') }}" class="hover:text-emerald-700 transition {{ request()->routeIs('public.news') ? 'text-emerald-700 font-bold' : '' }}">Berita</a>
                <a href="{{ route('public.faq') }}" class="hover:text-emerald-700 transition {{ request()->routeIs('public.faq') ? 'text-emerald-700 font-bold' : '' }}">FAQ</a>
                <a href="{{ route('public.contact') }}" class="hover:text-emerald-700 transition {{ request()->routeIs('public.contact') ? 'text-emerald-700 font-bold' : '' }}">Kontak</a>
                <a href="{{ route('public.chatbot.page') }}" class="hover:text-emerald-700 transition flex items-center gap-1 {{ request()->routeIs('public.chatbot.page') ? 'text-emerald-700 font-bold' : '' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Tanya AI</span>
                </a>
            </nav>

            <!-- Action Buttons & Mobile Hamburger -->
            <div class="flex items-center gap-2.5">
                @auth
                    <a href="{{ route('admin.monitoring.index') }}"
                        class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs transition">
                        Portal Admin
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold transition border border-slate-200">
                        Masuk
                    </a>
                    <a href="{{ route('register.rtrw') }}"
                        class="hidden sm:inline-flex px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs transition">
                        Daftar RT/RW
                    </a>
                @endauth

                <!-- Mobile Menu Button -->
                <button @click="mobileMenu = !mobileMenu" class="lg:hidden p-2 rounded-xl text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div x-show="mobileMenu" x-collapse class="lg:hidden bg-white border-b border-slate-200 px-4 pt-2 pb-4 space-y-1 text-xs font-semibold">
            <a href="{{ route('public.home') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-50">Beranda</a>
            <a href="{{ route('public.about') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-50">Tentang</a>
            <a href="{{ route('public.how-it-works') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-50">Cara Kerja</a>
            <a href="{{ route('public.technology') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-50">Teknologi</a>
            <a href="{{ route('public.gis') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-50">Peta Persebaran GIS</a>
            <a href="{{ route('public.stats') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-50">Statistik Dampak</a>
            <a href="{{ route('public.news') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-50">Berita</a>
            <a href="{{ route('public.faq') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-50">FAQ</a>
            <a href="{{ route('public.contact') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-50">Kontak & Sosialisasi</a>
            <a href="{{ route('public.chatbot.page') }}" class="block px-3 py-2 rounded-lg text-emerald-700 bg-emerald-50">Tanya Asisten AI</a>
            <div class="pt-2 border-t border-slate-100 flex gap-2">
                <a href="{{ route('login') }}" class="w-1/2 text-center py-2 rounded-lg bg-slate-100 text-slate-800">Masuk Portal</a>
                <a href="{{ route('register.rtrw') }}" class="w-1/2 text-center py-2 rounded-lg bg-emerald-600 text-white">Daftar RT/RW</a>
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
                <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider mb-3">Menu Informasi</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('public.about') }}" class="hover:text-emerald-700 transition">Tentang Program</a></li>
                    <li><a href="{{ route('public.how-it-works') }}" class="hover:text-emerald-700 transition">Cara Kerja Mesin</a></li>
                    <li><a href="{{ route('public.technology') }}" class="hover:text-emerald-700 transition">Teknologi & Spesifikasi</a></li>
                    <li><a href="{{ route('public.gis') }}" class="hover:text-emerald-700 transition">Peta Persebaran GIS</a></li>
                    <li><a href="{{ route('public.stats') }}" class="hover:text-emerald-700 transition">Statistik Reduksi Massa</a></li>
                    <li><a href="{{ route('public.news') }}" class="hover:text-emerald-700 transition">Berita & Informasi</a></li>
                    <li><a href="{{ route('public.faq') }}" class="hover:text-emerald-700 transition">FAQ</a></li>
                    <li><a href="{{ route('public.contact') }}" class="hover:text-emerald-700 transition">Hubungi Kami</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider mb-3">Portal Pengurus & RT/RW</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('register.rtrw') }}" class="hover:text-emerald-700 font-semibold text-emerald-800 transition">Pendaftaran Pengurus RT/RW</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-emerald-700 transition">Login Master Admin</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-emerald-700 transition">Login Admin Kota/Kabupaten</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-emerald-700 transition">Login Admin Lingkungan RT/RW</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-emerald-700 transition">Login Petugas Lapangan</a></li>
                    <li><a href="{{ route('public.chatbot.page') }}" class="hover:text-emerald-700 transition font-semibold text-emerald-700">Asisten AI Publik</a></li>
                </ul>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 pt-6 border-t border-slate-200/60 text-center text-slate-400 text-[11px]">
            &copy; {{ date('Y') }} ZeroWaste Green — Smart Green Composter Platform. Hak Cipta Dilindungi.
        </div>
    </footer>

    <!-- FLOATING AI CHATBOT WIDGET -->
    <div id="chatbot-container" class="fixed bottom-5 right-5 z-50">
        <!-- Chat Panel (Hidden by default) -->
        <div id="chat-panel" class="hidden mb-3 w-80 sm:w-96 bg-white rounded-2xl border border-slate-200 shadow-xl overflow-hidden flex flex-col h-[480px] animate-in fade-in slide-in-from-bottom-2 duration-200">
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
                <div class="flex items-center gap-1">
                    <a href="{{ route('public.chatbot.page') }}" title="Buka Halaman Penuh" class="text-white/80 hover:text-white p-1 rounded-lg">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                    <button onclick="toggleChat()" class="text-white/80 hover:text-white p-1 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Messages Log -->
            <div id="chat-messages" class="flex-1 p-3.5 overflow-y-auto space-y-3 text-xs">
                <div class="flex items-start gap-2">
                    <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold flex items-center justify-center shrink-0">
                        AI
                    </div>
                    <div class="bg-slate-100 rounded-2xl p-3 text-slate-700 leading-relaxed max-w-[85%]">
                        Halo! Saya asisten informasi publik ZeroWaste Green. Silakan tanyakan hal seputar panduan pemilahan sampah organik atau cara kerja Smart Green Composter!
                    </div>
                </div>
            </div>

            <!-- Quick Suggestions -->
            <div class="px-3 py-1.5 bg-slate-50 border-t border-slate-100 flex gap-1.5 overflow-x-auto text-[10px]">
                <button onclick="quickAsk('Sampah apa saja yang boleh dimasukkan?')" class="px-2 py-0.5 rounded-full bg-white border border-slate-200 text-slate-600 hover:border-emerald-500 shrink-0">
                    Sampah yang boleh?
                </button>
                <button onclick="quickAsk('Berapa lama proses komposting?')" class="px-2 py-0.5 rounded-full bg-white border border-slate-200 text-slate-600 hover:border-emerald-500 shrink-0">
                    Durasi mesin?
                </button>
            </div>

            <!-- Chat Input -->
            <form id="chat-form" onsubmit="handleChatSubmit(event)" class="p-2.5 bg-white border-t border-slate-200 flex gap-2">
                <input id="chat-input" type="text" placeholder="Ketik pertanyaan Anda..." required
                    class="flex-1 rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-800 focus:outline-none focus:border-emerald-600">
                <button type="submit" id="chat-send-btn" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition">
                    Kirim
                </button>
            </form>
        </div>

        <!-- Floating Trigger Button -->
        <button id="chatbot-trigger" onclick="toggleChat()" class="px-4 py-3 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-lg flex items-center gap-2 hover:scale-105 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>
            <span>Tanya Asisten AI</span>
        </button>
    </div>

    <!-- Chatbot Script -->
    <script>
        let chatToken = localStorage.getItem('sgc_chat_token') || ('tok_' + Math.random().toString(36).substring(2, 15));
        localStorage.setItem('sgc_chat_token', chatToken);

        function toggleChat() {
            const panel = document.getElementById('chat-panel');
            panel.classList.toggle('hidden');
            if (!panel.classList.contains('hidden')) {
                document.getElementById('chat-input').focus();
            }
        }

        function quickAsk(text) {
            document.getElementById('chat-input').value = text;
            document.getElementById('chat-form').dispatchEvent(new Event('submit'));
        }

        async function handleChatSubmit(e) {
            e.preventDefault();
            const input = document.getElementById('chat-input');
            const btn = document.getElementById('chat-send-btn');
            const messages = document.getElementById('chat-messages');
            const text = input.value.trim();

            if (!text) return;

            // User Message bubble
            messages.innerHTML += `
                <div class="flex items-start justify-end gap-2">
                    <div class="bg-emerald-600 text-white rounded-2xl p-3 leading-relaxed max-w-[85%]">
                        ${escapeHtml(text)}
                    </div>
                </div>
            `;
            input.value = '';
            messages.scrollTop = messages.scrollHeight;
            btn.disabled = true;

            // Loading bubble
            const loadingId = 'loading-' + Date.now();
            messages.innerHTML += `
                <div id="${loadingId}" class="flex items-start gap-2">
                    <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold flex items-center justify-center shrink-0">AI</div>
                    <div class="bg-slate-100 rounded-2xl p-3 text-slate-500 italic max-w-[85%]">
                        Mengetik...
                    </div>
                </div>
            `;
            messages.scrollTop = messages.scrollHeight;

            try {
                const res = await fetch("{{ route('public.chatbot') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        message: text,
                        session_token: chatToken,
                    })
                });
                const data = await res.json();
                document.getElementById(loadingId)?.remove();

                let sourceHtml = '';
                if (data.source) {
                    sourceHtml = `<div class="mt-2 pt-1 border-t border-slate-200 text-[10px] text-emerald-700 font-semibold">Sumber: ${escapeHtml(data.source)}</div>`;
                }

                messages.innerHTML += `
                    <div class="flex items-start gap-2">
                        <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold flex items-center justify-center shrink-0">AI</div>
                        <div class="bg-slate-100 rounded-2xl p-3 text-slate-800 leading-relaxed max-w-[85%]">
                            <p class="whitespace-pre-line">${escapeHtml(data.reply || 'Tidak ada tanggapan.')}</p>
                            ${sourceHtml}
                        </div>
                    </div>
                `;
            } catch (err) {
                document.getElementById(loadingId)?.remove();
                messages.innerHTML += `
                    <div class="flex items-start gap-2">
                        <div class="w-6 h-6 rounded-full bg-rose-100 text-rose-800 text-[10px] font-bold flex items-center justify-center shrink-0">!</div>
                        <div class="bg-rose-50 text-rose-800 rounded-2xl p-3 leading-relaxed max-w-[85%]">
                            Gagal menghubungi asisten AI. Periksa koneksi internet Anda.
                        </div>
                    </div>
                `;
            } finally {
                btn.disabled = false;
                messages.scrollTop = messages.scrollHeight;
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.innerText = text;
            return div.innerHTML;
        }
    </script>
    @stack('scripts')
</body>
</html>
