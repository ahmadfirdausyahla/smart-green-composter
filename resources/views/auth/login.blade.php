<x-guest-layout title="Masuk — Smart Green Composter">
    <div class="mb-5">
        <a href="{{ route('public.home') }}"
            class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-emerald-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Beranda</span>
        </a>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-semibold text-slate-700">Email Administrator</label>
            <div class="mt-1.5">
                <input id="email" name="email" type="email" autocomplete="email" required
                    value="{{ old('email') }}" placeholder="nama@zerowaste.id"
                    class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-600/20 text-sm transition">
            </div>
            @error('email')
                <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <div class="flex items-center justify-between">
                <label for="password" class="block text-sm font-semibold text-slate-700">Kata Sandi</label>
            </div>
            <div class="mt-1.5">
                <input id="password" name="password" type="password" autocomplete="current-password" required
                    placeholder="Masukkan kata sandi"
                    class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-600/20 text-sm transition">
            </div>
            @error('password')
                <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input id="remember" name="remember" type="checkbox"
                    class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                <label for="remember" class="ml-2 block text-xs text-slate-600">Ingat sesi saya</label>
            </div>
        </div>

        <div>
            <button type="submit"
                class="flex w-full justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2 transition active:scale-[0.99]">
                Masuk ke Sistem
            </button>
        </div>
    </form>

    <div class="mt-6 text-center text-xs text-slate-600">
        Ketua RT/RW baru ingin mendaftar?
        <a href="{{ route('register.rtrw') }}" class="font-semibold text-emerald-600 hover:text-emerald-700 underline underline-offset-2 ml-1">
            Daftar Akun RT/RW
        </a>
    </div>
</x-guest-layout>
