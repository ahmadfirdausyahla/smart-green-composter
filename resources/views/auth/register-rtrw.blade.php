<x-guest-layout title="Pendaftaran Admin RT/RW — Smart Green Composter" heading="Pendaftaran Mandiri Admin RT/RW" subheading="Isi data wilayah dan administrasi lingkungan Anda untuk verifikasi Admin Kota">
    <div class="mb-5">
        <a href="{{ route('public.home') }}"
            class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-emerald-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Beranda</span>
        </a>
    </div>

    <form method="POST" action="{{ route('register.rtrw') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Nama Lengkap Ketua / Pengurus RT/RW</label>
            <input id="name" name="name" type="text" required value="{{ old('name') }}" placeholder="Contoh: Bpk. H. Hendra Setiawan"
                class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-600/20">
            @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Email Aktif</label>
                <input id="email" name="email" type="email" required value="{{ old('email') }}" placeholder="admin.rt@zerowaste.id"
                    class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-600/20">
                @error('email') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="phone_number" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Nomor WhatsApp / HP</label>
                <input id="phone_number" name="phone_number" type="tel" required value="{{ old('phone_number') }}" placeholder="081234567890"
                    class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-600/20">
                @error('phone_number') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Kata Sandi</label>
                <input id="password" name="password" type="password" required
                    class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-600/20">
                @error('password') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Konfirmasi Sandi</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                    class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-600/20">
            </div>
        </div>

        <!-- Wilayah Bertingkat -->
        <div class="pt-2 border-t border-slate-100">
            <p class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">Data Wilayah Kewenangan</p>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="province_id" class="block text-xs font-medium text-slate-600">Provinsi</label>
                    <select id="province_id" name="province_id" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-emerald-600 focus:outline-none">
                        <option value="">Pilih Provinsi</option>
                        @foreach ($provinces as $prov)
                            <option value="{{ $prov->id }}" {{ old('province_id') == $prov->id ? 'selected' : '' }}>{{ $prov->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="city_id" class="block text-xs font-medium text-slate-600">Kota / Kabupaten</label>
                    <select id="city_id" name="city_id" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-emerald-600 focus:outline-none">
                        <option value="">Pilih Kota</option>
                        @foreach ($provinces as $prov)
                            @foreach ($prov->cities as $cty)
                                <option value="{{ $cty->id }}" {{ old('city_id') == $cty->id ? 'selected' : '' }}>{{ $cty->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                <div>
                    <label for="district_id" class="block text-xs font-medium text-slate-600">Kecamatan</label>
                    <select id="district_id" name="district_id" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-emerald-600 focus:outline-none">
                        <option value="">Pilih Kecamatan</option>
                        @foreach ($provinces as $prov)
                            @foreach ($prov->cities as $cty)
                                @foreach ($cty->districts as $dst)
                                    <option value="{{ $dst->id }}" {{ old('district_id') == $dst->id ? 'selected' : '' }}>{{ $dst->name }} ({{ $cty->name }})</option>
                                @endforeach
                            @endforeach
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="village_id" class="block text-xs font-medium text-slate-600">Kelurahan / Desa</label>
                    <select id="village_id" name="village_id" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-emerald-600 focus:outline-none">
                        <option value="">Pilih Kelurahan</option>
                        @foreach ($provinces as $prov)
                            @foreach ($prov->cities as $cty)
                                @foreach ($cty->districts as $dst)
                                    @foreach ($dst->villages as $vlg)
                                        <option value="{{ $vlg->id }}" {{ old('village_id') == $vlg->id ? 'selected' : '' }}>{{ $vlg->name }} ({{ $dst->name }})</option>
                                    @endforeach
                                @endforeach
                            @endforeach
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-3">
                <div>
                    <label for="rt_number" class="block text-xs font-medium text-slate-600">Nomor RT</label>
                    <input id="rt_number" name="rt_number" type="text" required placeholder="03" value="{{ old('rt_number') }}"
                        class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-emerald-600 focus:outline-none">
                </div>
                <div>
                    <label for="rw_number" class="block text-xs font-medium text-slate-600">Nomor RW</label>
                    <input id="rw_number" name="rw_number" type="text" required placeholder="05" value="{{ old('rw_number') }}"
                        class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-emerald-600 focus:outline-none">
                </div>
            </div>
        </div>

        <div>
            <label for="supporting_info" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Keterangan / Alamat Lingkungan</label>
            <textarea id="supporting_info" name="supporting_info" rows="2" placeholder="Sebutkan alamat balai RT/RW atau kontak perwakilan..."
                class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-emerald-600 focus:outline-none">{{ old('supporting_info') }}</textarea>
        </div>

        <div class="pt-2">
            <button type="submit"
                class="flex w-full justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2 transition">
                Kirim Permohonan Pendaftaran
            </button>
        </div>
    </form>

    <div class="mt-4 text-center text-xs text-slate-600">
        Sudah memiliki akun aktif?
        <a href="{{ route('login') }}" class="font-semibold text-emerald-600 hover:text-emerald-700 underline underline-offset-2 ml-1">
            Masuk ke Akun
        </a>
    </div>
</x-guest-layout>
