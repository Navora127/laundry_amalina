@extends('layouts.app')

@section('title', 'LaundryKu — Laundry rapi, tanpa ribet')

@section('content')
<section class="blueprint-grid relative overflow-hidden bg-brand-950 text-white">
    <div class="absolute -right-32 -top-40 h-96 w-96 rounded-full bg-brand-500/20 blur-3xl"></div>
    <div class="absolute -bottom-40 left-1/4 h-80 w-80 rounded-full bg-sky-400/10 blur-3xl"></div>
    <div class="container-app relative grid gap-12 py-20 lg:grid-cols-[1.1fr_.9fr] lg:items-center lg:py-28">
        <div>
            <span class="inline-flex items-center gap-2 rounded-full border border-brand-300/30 bg-white/5 px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.18em] text-blue-100">
                <span class="h-2 w-2 rounded-full bg-brand-300"></span>
                Laundry modern untuk rutinitas Anda
            </span>
            <p class="mt-8 font-mono text-xs uppercase tracking-[0.25em] text-brand-300">LAUNDRYKU / 01</p>
            <h1 class="mt-4 max-w-3xl text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">Cuci selesai.<br><span class="text-brand-300">Lacak tanpa worries.</span></h1>
            <p class="mt-6 max-w-xl text-base leading-7 text-blue-100/75 sm:text-lg">Pilih layanan, atur alamat, dan pantau proses laundry dari satu portal yang aman dan transparan.</p>
            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('catalog.index') }}" class="btn-light">Lihat katalog <span aria-hidden="true">→</span></a>
                @guest
                    <a href="{{ route('register') }}" class="btn-ghost-light">Mulai laundry</a>
                @else
                    <a href="{{ auth()->user()->isStaff() ? route('admin.dashboard') : route('customer.orders.create') }}" class="btn-ghost-light">Pesan sekarang</a>
                @endguest
            </div>
            <div class="mt-10 flex flex-wrap gap-x-8 gap-y-3 text-sm text-blue-100/65">
                <span>✓ Harga transparan</span>
                <span>✓ Status real-time</span>
                <span>✓ Data terlindungi</span>
            </div>
        </div>
        <div class="relative mx-auto w-full max-w-md">
            <div class="absolute -inset-5 rounded-[2.5rem] bg-brand-400/10 blur-2xl"></div>
            <div class="blueprint-grid relative rounded-[2rem] border border-white/15 bg-white/10 p-5 shadow-2xl backdrop-blur">
                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                    <div>
                        <p class="text-xs uppercase tracking-wider text-blue-100/55">Pesanan terbaru</p>
                        <p class="mt-1 font-semibold">LDR-20260925-8K2PQA</p>
                    </div>
                    <span class="rounded-full bg-amber-300/15 px-3 py-1 text-xs font-semibold text-amber-200">Diproses</span>
                </div>
                <div class="space-y-5 py-6">
                    <div class="flex gap-4">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-brand-400/20 text-brand-200">1</span>
                        <div><p class="text-sm font-semibold">Pesanan diterima</p><p class="text-xs text-blue-100/55">Baru saja · laundry sedang diperiksa</p></div>
                    </div>
                    <div class="ml-4 h-7 border-l border-dashed border-brand-300/40"></div>
                    <div class="flex gap-4">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-brand-400/20 text-brand-200">2</span>
                        <div><p class="text-sm font-semibold">Cuci & jemur</p><p class="text-xs text-blue-100/55">Estimasi 1×24 jam</p></div>
                    </div>
                    <div class="ml-4 h-7 border-l border-dashed border-brand-300/40"></div>
                    <div class="flex gap-4 opacity-50">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/10 text-blue-100">3</span>
                        <div><p class="text-sm font-semibold">Siap diambil</p><p class="text-xs text-blue-100/55">Notifikasi akan dikirim</p></div>
                    </div>
                </div>
                <div class="rounded-2xl bg-brand-950/60 p-4 text-xs text-blue-100/70">Perubahan status dicatat otomatis dan dapat Anda pantau kapan saja.</div>
            </div>
        </div>
    </div>
</section>

<section class="container-app py-16 sm:py-20">
    <div class="mx-auto max-w-2xl">
        <p class="eyebrow">Mulai dari katalog</p>
        <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Layanan yang sesuai kebutuhan</h2>
        <p class="mt-4 text-slate-600">Harga dan estimasi durasi selalu terlihat sebelum Anda memesan.</p>
    </div>
    <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($categories as $category)
            <div class="card p-6 transition hover:-translate-y-1 hover:shadow-lg">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="font-mono text-xs font-bold uppercase tracking-wider text-brand-600">{{ str($loop->iteration)->padLeft(2, '0') }}</p>
                        <h3 class="mt-2 text-lg font-semibold text-slate-900">{{ $category->name }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-500">{{ $category->description }}</p>
                    </div>
                    <span class="rounded-lg bg-brand-50 px-2.5 py-1 text-xs font-semibold text-brand-700">{{ $category->services->count() }} layanan</span>
                </div>
                <ul class="mt-5 space-y-3 border-t border-slate-100 pt-4">
                    @foreach ($category->services->take(3) as $service)
                        <li class="flex items-center justify-between gap-3 text-sm">
                            <span class="text-slate-600">{{ $service->name }}</span>
                            <span class="font-semibold text-slate-900">Rp{{ number_format($service->price, 0, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('catalog.index', ['category' => $category->slug]) }}" class="mt-5 inline-flex text-sm font-semibold text-brand-700 hover:text-brand-800">Lihat kategori <span class="ml-1">→</span></a>
            </div>
        @empty
            <div class="card col-span-full p-8 text-center text-slate-500">Katalog sedang disiapkan.</div>
        @endforelse
    </div>
</section>

<section class="border-y border-brand-100 bg-brand-50/80 py-14">
    <div class="container-app grid gap-8 md:grid-cols-3">
        <div><p class="font-mono text-3xl font-bold text-brand-700">24/7</p><p class="mt-1 text-sm text-slate-600">Pesanan dapat dibuat kapan saja.</p></div>
        <div><p class="font-mono text-3xl font-bold text-brand-700">100%</p><p class="mt-1 text-sm text-slate-600">Harga dihitung ulang oleh server.</p></div>
        <div><p class="font-mono text-3xl font-bold text-brand-700">1×</p><p class="mt-1 text-sm text-slate-600">Tempat untuk melihat semua status laundry.</p></div>
    </div>
</section>

<section class="container-app py-16">
    <div class="grid gap-5 md:grid-cols-3">
        <div class="card p-6"><p class="eyebrow">Harga jelas</p><h3 class="mt-3 text-lg font-semibold">Tidak ada biaya tersembunyi.</h3><p class="mt-2 text-sm leading-6 text-slate-500">Estimasi dihitung oleh server dan harga aktual diverifikasi sebelum diproses.</p></div>
        <div class="card p-6"><p class="eyebrow">Status jelas</p><h3 class="mt-3 text-lg font-semibold">Pantau setiap tahap.</h3><p class="mt-2 text-sm leading-6 text-slate-500">Riwayat status tersimpan dan dapat dilihat oleh pelanggan sesuai haknya.</p></div>
        <div class="card p-6"><p class="eyebrow">Akses mudah</p><h3 class="mt-3 text-lg font-semibold">Satu portal, banyak peran.</h3><p class="mt-2 text-sm leading-6 text-slate-500">Pelanggan, kasir, dan administrator memiliki tampilan yang fokus.</p></div>
    </div>
</section>
@endsection
