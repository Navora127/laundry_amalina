<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#16877f">
    <meta name="description" content="LaundryKu — laundry cepat, rapi, dan mudah dipantau.">
    @if (file_exists(public_path('build/manifest.webmanifest')))
        <link rel="manifest" href="/build/manifest.webmanifest">
    @endif
    <link rel="icon" href="/icons/favicon-32x32.png" type="image/png">
    <title>@yield('title', config('app.name', 'LaundryKu'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <div
        x-data="{ updateAvailable: false }"
        @pwa:update-ready.window="updateAvailable = true"
        class="fixed inset-x-0 bottom-4 z-50 px-4"
        x-cloak
        x-show="updateAvailable"
    >
        <div class="mx-auto flex max-w-lg items-center justify-between gap-4 rounded-2xl bg-slate-900 px-4 py-3 text-sm text-white shadow-xl">
            <span>Versi baru LaundryKu sudah tersedia.</span>
            <button
                type="button"
                class="shrink-0 rounded-lg bg-brand-500 px-3 py-1.5 font-semibold hover:bg-brand-400"
                @click="window.LaundryKu.updateServiceWorker(true)"
            >
                Perbarui
            </button>
        </div>
    </div>

    <header class="border-b border-slate-200/80 bg-white/90 backdrop-blur">
        <div class="container-app flex h-16 items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 font-bold tracking-tight text-ink">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-600 text-lg text-white shadow-sm">L</span>
                <span>Laundry<span class="text-brand-600">Ku</span></span>
            </a>

            <nav class="hidden items-center gap-1 text-sm font-medium text-slate-600 md:flex" aria-label="Navigasi utama">
                <a href="{{ route('catalog.index') }}" class="rounded-lg px-3 py-2 hover:bg-brand-50 hover:text-brand-700">Katalog</a>
                @auth
                    @if (auth()->user()->isStaff())
                        <a href="{{ route('admin.dashboard') }}" class="rounded-lg px-3 py-2 hover:bg-brand-50 hover:text-brand-700">Dashboard</a>
                        <a href="{{ route('admin.orders.index') }}" class="rounded-lg px-3 py-2 hover:bg-brand-50 hover:text-brand-700">Pesanan</a>
                        <a href="{{ route('admin.catalog.services') }}" class="rounded-lg px-3 py-2 hover:bg-brand-50 hover:text-brand-700">Katalog</a>
                        <a href="{{ route('admin.reports.index') }}" class="rounded-lg px-3 py-2 hover:bg-brand-50 hover:text-brand-700">Laporan</a>
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.staff.index') }}" class="rounded-lg px-3 py-2 hover:bg-brand-50 hover:text-brand-700">Staff</a>
                        @endif
                    @else
                        <a href="{{ route('customer.orders.index') }}" class="rounded-lg px-3 py-2 hover:bg-brand-50 hover:text-brand-700">Pesanan saya</a>
                        <a href="{{ route('customer.addresses.index') }}" class="rounded-lg px-3 py-2 hover:bg-brand-50 hover:text-brand-700">Alamat</a>
                    @endif
                @endauth
            </nav>

            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('settings') }}" class="hidden text-sm font-medium text-slate-500 hover:text-brand-700 lg:inline">{{ auth()->user()->name }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn-secondary" type="submit">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-secondary">Masuk</a>
                    <a href="{{ route('register') }}" class="btn-primary hidden sm:inline-flex">Daftar</a>
                @endauth
            </div>
        </div>
    </header>

    <main>
        @include('components.flash')
        @yield('content')
    </main>

    <footer class="mt-16 border-t border-slate-200/80 bg-white">
        <div class="container-app flex flex-col gap-2 py-8 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ date('Y') }} LaundryKu. Seluruh hak cipta dilindungi.</p>
            <p>Laundry bersih, aman, dan mudah dipantau.</p>
        </div>
    </footer>
</body>
</html>
