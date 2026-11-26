<x-public-layout title="Asisten AI Edukasi — Smart Green Composter">

    <section class="py-8 bg-slate-50 min-h-[calc(100vh-140px)] flex flex-col justify-center">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 w-full flex-1 flex flex-col">
            <!-- Header Card -->
            <div class="bg-white rounded-t-2xl border border-b-0 border-slate-200/80 p-4 sm:p-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white font-bold text-sm flex items-center justify-center shadow-xs">
                        AI
                    </div>
                    <div>
                        <h1 class="text-sm sm:text-base font-bold text-slate-900 leading-tight">Asisten Pintar ZeroWaste Green</h1>
                        <p class="text-xs text-slate-500">Edukasi pemilahan sampah organik & panduan operasional komposter (RAG Powered)</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button onclick="clearChat()" class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-[11px] font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Percakapan Baru
                    </button>
                    <span class="hidden sm:inline-flex px-2 py-1 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                        Online
                    </span>
                </div>
            </div>

            <!-- Chat Messages Log Container -->
            <div id="page-chat-box" class="bg-white border-x border-slate-200/80 p-5 overflow-y-auto flex-1 min-h-[420px] max-h-[560px] space-y-4 text-xs">
                <!-- Welcome Assistant Message -->
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[11px] flex items-center justify-center shrink-0">
                        AI
                    </div>
                    <div class="max-w-lg bg-emerald-50/70 border border-emerald-100/80 rounded-2xl p-4 text-slate-800 leading-relaxed shadow-2xs">
                        <p class="font-semibold text-emerald-900 mb-1">Halo! Saya Asisten Virtual Smart Green Composter.</p>
                        <p>Saya dapat membantu Anda memahami jenis sampah dapur yang boleh/dilarang dimasukkan, proses kerja dekomposisi 4 jam, batas keselamatan suhu, dan cara pengurus RT/RW mendapatkan unit mesin.</p>
                    </div>
                </div>
            </div>

            <!-- Suggested Questions Bar -->
            <div class="bg-white border-x border-slate-200/80 px-4 py-2 border-t border-slate-100 flex flex-wrap gap-2 text-[11px]">
                <span class="text-slate-400 font-medium py-1">Pertanyaan Cepat:</span>
                <button onclick="askPrompt('Sampah apa saja yang boleh dan dilarang dimasukkan?')" class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-emerald-50 hover:text-emerald-800 text-slate-700 transition">
                    Sampah yang boleh & dilarang?
                </button>
                <button onclick="askPrompt('Berapa lama durasi pengolahan dan berapa persen reduksi massanya?')" class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-emerald-50 hover:text-emerald-800 text-slate-700 transition">
                    Durasi & reduksi massa?
                </button>
                <button onclick="askPrompt('Bagaimana mekanisme proteksi jika suhu melebihi batas 65 derajat?')" class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-emerald-50 hover:text-emerald-800 text-slate-700 transition">
                    Sistem proteksi suhu?
                </button>
            </div>

            <!-- Chat Input Footer -->
            <div class="bg-white rounded-b-2xl border border-t border-slate-200/80 p-3 sm:p-4">
                <form id="page-chat-form" onsubmit="sendPageMessage(event)" class="flex gap-2">
                    <input id="page-chat-input" type="text" required placeholder="Tanyakan seputar pemilahan sampah organik atau panduan mesin..."
                        class="flex-1 rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-emerald-600">
                    <button type="submit" id="page-chat-btn" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-xs transition shrink-0 flex items-center gap-1.5">
                        <span>Kirim</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        let chatSessionToken = localStorage.getItem('sgc_chat_token') || ('tok_' + Math.random().toString(36).substring(2, 15));
        localStorage.setItem('sgc_chat_token', chatSessionToken);

        function appendMessage(role, text, source) {
            const box = document.getElementById('page-chat-box');
            const isUser = role === 'user';

            const wrap = document.createElement('div');
            wrap.className = isUser ? 'flex items-start justify-end gap-3' : 'flex items-start gap-3';

            const bubble = document.createElement('div');
            bubble.className = isUser
                ? 'max-w-lg bg-emerald-600 text-white rounded-2xl p-4 text-xs leading-relaxed shadow-xs'
                : 'max-w-lg bg-emerald-50/70 border border-emerald-100/80 text-slate-800 rounded-2xl p-4 text-xs leading-relaxed shadow-2xs';

            let contentHtml = `<p class="whitespace-pre-line">${escapeHtml(text)}</p>`;
            if (source && !isUser) {
                contentHtml += `<div class="mt-2.5 pt-2 border-t border-emerald-200/60 text-[10px] text-emerald-800 font-semibold flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>Sumber: ${escapeHtml(source)}</span>
                </div>`;
            }

            bubble.innerHTML = contentHtml;

            const avatar = document.createElement('div');
            avatar.className = isUser
                ? 'w-7 h-7 rounded-full bg-slate-800 text-white font-bold text-[11px] flex items-center justify-center shrink-0'
                : 'w-7 h-7 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[11px] flex items-center justify-center shrink-0';
            avatar.innerText = isUser ? 'U' : 'AI';

            if (isUser) {
                wrap.appendChild(bubble);
                wrap.appendChild(avatar);
            } else {
                wrap.appendChild(avatar);
                wrap.appendChild(bubble);
            }

            box.appendChild(wrap);
            box.scrollTop = box.scrollHeight;
        }

        async function sendPageMessage(e) {
            e.preventDefault();
            const input = document.getElementById('page-chat-input');
            const btn = document.getElementById('page-chat-btn');
            const message = input.value.trim();
            if (!message) return;

            appendMessage('user', message);
            input.value = '';
            btn.disabled = true;
            btn.innerHTML = `<span class="animate-spin">&bull;</span> Mengetik...`;

            try {
                const res = await fetch("{{ route('public.chatbot') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        message: message,
                        session_token: chatSessionToken,
                    })
                });

                const data = await res.json();
                appendMessage('assistant', data.reply || 'Tidak ada balasan.', data.source);
            } catch (err) {
                appendMessage('assistant', 'Terjadi gangguan jaringan dalam menghubungi layanan chatbot. Silakan coba sesaat lagi.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = `<span>Kirim</span> <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>`;
            }
        }

        function askPrompt(promptText) {
            document.getElementById('page-chat-input').value = promptText;
            document.getElementById('page-chat-form').dispatchEvent(new Event('submit'));
        }

        function clearChat() {
            chatSessionToken = 'tok_' + Math.random().toString(36).substring(2, 15);
            localStorage.setItem('sgc_chat_token', chatSessionToken);
            const box = document.getElementById('page-chat-box');
            box.innerHTML = `
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[11px] flex items-center justify-center shrink-0">
                        AI
                    </div>
                    <div class="max-w-lg bg-emerald-50/70 border border-emerald-100/80 rounded-2xl p-4 text-slate-800 leading-relaxed shadow-2xs">
                        <p class="font-semibold text-emerald-900 mb-1">Percakapan Baru Dimulai.</p>
                        <p>Silakan tanyakan hal apa saja seputar operasional Smart Green Composter dan edukasi sampah organik.</p>
                    </div>
                </div>
            `;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.innerText = text;
            return div.innerHTML;
        }
    </script>
    @endpush

</x-public-layout>
