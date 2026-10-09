<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#215fce">
    <title>@yield('title', 'Masuk') — {{ config('app.name', 'LaundryKu') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-brand-950">
    <div class="blueprint-grid relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-10">
        <div class="absolute -left-24 -top-24 h-72 w-72 rounded-full bg-brand-500/25 blur-3xl"></div>
        <div class="absolute -bottom-32 -right-20 h-96 w-96 rounded-full bg-sky-400/15 blur-3xl"></div>
        <div class="relative w-full max-w-md">
            <a href="{{ route('home') }}" class="mb-8 flex items-center justify-center gap-2.5 text-lg font-bold text-white">
                <span class="flex h-10 w-10 items-center justify-center rounded-lg border border-brand-300/30 bg-brand-600 text-sm font-black tracking-tighter text-white">LK</span>
                Laundry<span class="text-brand-300">Ku</span>
            </a>
            <div class="rounded-3xl border border-brand-100/20 bg-white p-6 shadow-2xl shadow-brand-950/40 sm:p-8">
                @yield('content')
            </div>
            <p class="mt-6 text-center text-xs text-blue-100/60">LaundryKu · laundry harian lebih mudah</p>
        </div>
    </div>
</body>
</html>
