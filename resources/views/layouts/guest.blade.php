<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Masuk' }} — Smart Green Composter</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <!-- Logo Brand -->
        <a href="{{ route('public.home') }}" class="inline-flex items-center gap-2.5 group">
            <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white shadow-sm shadow-emerald-600/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </div>
            <div class="text-left">
                <div class="text-base font-bold text-slate-900 tracking-tight leading-tight">Smart Green Composter</div>
                <div class="text-xs font-medium text-emerald-700 tracking-wide">ZeroWaste Green IoT</div>
            </div>
        </a>
        <h2 class="mt-6 text-2xl font-bold tracking-tight text-slate-900">{{ $heading ?? 'Masuk ke Portal Sistem' }}</h2>
        <p class="mt-2 text-sm text-slate-600">{{ $subheading ?? 'Sistem Monitoring dan Kendali Mesin Komposter Organik' }}</p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <!-- Alert Messages -->
        @if (session('error'))
            <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 p-4 text-sm text-rose-800 flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800 flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white py-8 px-6 shadow-sm border border-slate-200/80 rounded-2xl sm:px-10">
            {{ $slot }}
        </div>

        <div class="mt-6 text-center text-xs text-slate-500">
            &copy; {{ date('Y') }} ZeroWaste Green — Platform Pengolahan Sampah Organik Cerdas.
        </div>
    </div>
</body>
</html>
