<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Portal Kontrol' }} — Smart Green Composter</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    
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
<body class="h-full flex flex-col md:flex-row text-slate-800 antialiased selection:bg-emerald-500 selection:text-white">

    <!-- SIDEBAR NAVIGATION -->
    <aside class="w-full md:w-64 bg-white border-r border-slate-200/90 flex flex-col shrink-0">
        <!-- Logo & Platform Info -->
        <div class="h-16 px-5 border-b border-slate-100 flex items-center justify-between">
            <a href="{{ route('public.home') }}" class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center text-white shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
                <div>
                    <span class="block text-sm font-bold text-slate-900 leading-none">Smart Green</span>
                    <span class="text-[10px] font-semibold text-emerald-700 tracking-wider">ZERO WASTE GREEN</span>
                </div>
            </a>
            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                v1.0
            </span>
        </div>

        <!-- Role Badge & Jurisdiction Banner -->
        <div class="p-3.5 mx-3 my-2.5 bg-slate-50 rounded-xl border border-slate-200/60">
            <div class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Peran Akses</div>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-bold text-slate-900">{{ auth()->user()->role->label() }}</span>
            </div>
            <div class="mt-2 pt-2 border-t border-slate-200/60 text-[11px] text-slate-600 flex items-center gap-1.5 truncate" title="{{ auth()->user()->jurisdiction_name }}">
                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="truncate">{{ auth()->user()->jurisdiction_name }}</span>
            </div>
        </div>

        <!-- Adaptive Navigation Links -->
        <nav class="flex-1 px-3 space-y-1 overflow-y-auto">
            @php
                $user = auth()->user();
                $isMaster = $user->isMasterAdmin();
                $isKota = $user->isAdminKota();
                $isRtRw = $user->isAdminRtRw();
                $isLapangan = $user->isAdminLapangan();
            @endphp

            @if (! $isLapangan)
                <!-- 1. Dashboard (Statistik Wilayah) -->
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.dashboard') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>Dashboard Wilayah</span>
                </a>
            @endif

            <!-- 2. Monitoring & Remote Control (Menu 1 untuk Admin Lapangan & Semua Role) -->
            <a href="{{ route('admin.monitoring.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.monitoring.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('admin.monitoring.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span>Monitoring & Remote</span>
            </a>

            @if (! $isLapangan)
                <!-- 3. Persetujuan Pengoperasian -->
                <a href="{{ route('admin.approvals.index') }}"
                    class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.approvals.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 {{ request()->routeIs('admin.approvals.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Persetujuan Operasi</span>
                    </div>
                </a>
            @endif

            <!-- 4. Kondisi Alat & Laporan Kerusakan (Menu 2 untuk Admin Lapangan & Semua Role) -->
            <a href="{{ route('admin.maintenance.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.maintenance.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('admin.maintenance.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Kondisi Alat & Kerusakan</span>
            </a>

            @if (! $isLapangan)
                <!-- 5. Laporan & Reduksi Massa (Section 21) -->
                <a href="{{ route('admin.reports.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.reports.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.reports.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Laporan & Analitik</span>
                </a>
            @endif

            @if ($isMaster || $isKota)
                <!-- 6. Verifikasi Pendaftaran RT/RW -->
                <a href="{{ route('admin.registration-requests.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.registration-requests.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.registration-requests.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    <span>Pendaftaran RT/RW</span>
                </a>

                <!-- 7. Inventaris & Distribusi -->
                <a href="{{ route('admin.inventory.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.inventory.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.inventory.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span>Inventaris & Distribusi</span>
                </a>
            @endif

            @if ($isRtRw || $isKota || $isMaster)
                <!-- 8. Permintaan Unit Alat -->
                <a href="{{ route('admin.device-requests.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.device-requests.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.device-requests.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span>Pengajuan Unit Alat</span>
                </a>
            @endif

            @if ($isRtRw || $isMaster)
                <!-- 9. Jadwal Petugas Piket (Dikelola oleh RT/RW) -->
                <a href="{{ route('admin.schedules.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.schedules.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.schedules.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Jadwal Petugas</span>
                </a>
            @endif

            @if ($isMaster || $isKota)
                <!-- 10. Kelola Basis Pengetahuan AI -->
                <a href="{{ route('admin.knowledge-base.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.knowledge-base.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.knowledge-base.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span>Knowledge Base AI</span>
                </a>

                <!-- 11. Audit Log Sistem -->
                <a href="{{ route('admin.audit-logs.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.audit-logs.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.audit-logs.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Audit Log</span>
                </a>
            @endif
        </nav>

            <!-- Divider -->
            <div class="pt-3 pb-1">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3">Tautan Eksternal</div>
            </div>

            <a href="{{ route('public.gis') }}" target="_blank"
                class="flex items-center gap-3 px-3 py-1.5 rounded-xl text-xs font-medium text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
                <span>Peta GIS Publik</span>
            </a>

            <a href="{{ route('public.home') }}" target="_blank"
                class="flex items-center gap-3 px-3 py-1.5 rounded-xl text-xs font-medium text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                <span>Portal Warga</span>
            </a>
        </nav>

        <!-- User Profile & Logout Section -->
        <div class="p-3 border-t border-slate-100 mt-auto">
            <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 border border-slate-200/60">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-emerald-600/10 text-emerald-800 font-bold text-xs flex items-center justify-center shrink-0 border border-emerald-600/20">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-slate-800 truncate">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-slate-500 truncate">{{ auth()->user()->email }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Keluar" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Top Navbar -->
        <header class="h-16 bg-white border-b border-slate-200/90 px-6 flex items-center justify-between sticky top-0 z-20">
            <div>
                <h1 class="text-base font-bold text-slate-900 leading-tight">{{ $headerTitle ?? 'Portal Kendali Smart Green Composter' }}</h1>
                <p class="text-xs text-slate-500">{{ $headerSubtitle ?? 'Monitoring telemetri sensor dan kendali mesin' }}</p>
            </div>
            <div class="flex items-center gap-3">
                <!-- System Status Indicator -->
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-200 text-xs font-medium text-emerald-800">
                    <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                    <span>Sistem IoT Siap</span>
                </div>

                <!-- Clock -->
                <div class="text-xs text-slate-500 font-mono-numeric">
                    {{ now()->translatedFormat('d M Y, H:i') }} WIB
                </div>
            </div>
        </header>

        <!-- Flash Banners -->
        <div class="px-6 pt-5">
            @if (session('success'))
                <div class="mb-5 rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-xs font-medium text-emerald-800 flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <div class="flex-1">{{ session('success') }}</div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-5 rounded-xl bg-rose-50 border border-rose-200 p-4 text-xs font-medium text-rose-800 flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="flex-1">{{ session('error') }}</div>
                </div>
            @endif
        </div>

        <!-- Dynamic Body Slot -->
        <main class="flex-1 p-6 pt-0">
            {{ $slot }}
        </main>
    </div>

</body>
</html>
