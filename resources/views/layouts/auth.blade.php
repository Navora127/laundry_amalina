<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#16877f">
    <title>@yield('title', 'Masuk') — {{ config('app.name', 'LaundryKu') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950">
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-10">
        <div class="absolute -left-24 -top-24 h-72 w-72 rounded-full bg-brand-500/20 blur-3xl"></div>
        <div class="absolute -bottom-32 -right-20 h-96 w-96 rounded-full bg-coral/20 blur-3xl"></div>
        <div class="relative w-full max-w-md">
            <a href="{{ route('home') }}" class="mb-8 flex items-center justify-center gap-2.5 text-lg font-bold text-white">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-500 text-xl text-white">L</span>
                Laundry<span class="text-brand-300">Ku</span>
            </a>
            <div class="rounded-3xl border border-white/10 bg-white p-6 shadow-2xl shadow-slate-950/30 sm:p-8">
                @yield('content')
            </div>
            <p class="mt-6 text-center text-xs text-slate-400">LaundryKu · laundry harian lebih mudah</p>
        </div>
    </div>
</body>
</html>
