<x-guest-layout title="Masuk — Smart Green Composter">
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-semibold text-slate-700">Email Administrator</label>
            <div class="mt-1.5">
                <input id="email" name="email" type="email" autocomplete="email" required
                    value="{{ old('email', 'master@zerowaste.id') }}"
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
                    value="password"
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

    <!-- Quick Role Tester for Development & Evaluation -->
    <div class="mt-6 pt-5 border-t border-slate-100">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2.5">Akses Uji Coba Cepat (Multi-Role):</p>
        <div class="grid grid-cols-2 gap-2 text-xs">
            <button type="button" onclick="setRole('master@zerowaste.id', 'password')"
                class="text-left p-2 rounded-lg border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50/50 transition">
                <div class="font-semibold text-slate-800">Master Admin</div>
                <div class="text-[10px] text-slate-500 truncate">master@zerowaste.id</div>
            </button>
            <button type="button" onclick="setRole('admin.bogor@zerowaste.id', 'password')"
                class="text-left p-2 rounded-lg border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50/50 transition">
                <div class="font-semibold text-slate-800">Admin Kota Bogor</div>
                <div class="text-[10px] text-slate-500 truncate">admin.bogor@zerowaste.id</div>
            </button>
            <button type="button" onclick="setRole('admin.rtrw@zerowaste.id', 'password')"
                class="text-left p-2 rounded-lg border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50/50 transition">
                <div class="font-semibold text-slate-800">Admin RT 03 / RW 05</div>
                <div class="text-[10px] text-slate-500 truncate">admin.rtrw@zerowaste.id</div>
            </button>
            <button type="button" onclick="setRole('petugas@zerowaste.id', 'password')"
                class="text-left p-2 rounded-lg border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50/50 transition">
                <div class="font-semibold text-slate-800">Petugas Lapangan</div>
                <div class="text-[10px] text-slate-500 truncate">petugas@zerowaste.id</div>
            </button>
        </div>
    </div>

    <div class="mt-6 text-center text-xs text-slate-600">
        Ketua RT/RW baru ingin mendaftar?
        <a href="{{ route('register.rtrw') }}" class="font-semibold text-emerald-600 hover:text-emerald-700 underline underline-offset-2 ml-1">
            Daftar Akun RT/RW
        </a>
    </div>

    <script>
        function setRole(email, pass) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = pass;
        }
    </script>
</x-guest-layout>
