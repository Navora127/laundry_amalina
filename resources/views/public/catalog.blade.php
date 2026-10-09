@extends('layouts.app')

@section('title', 'Katalog layanan — LaundryKu')

@section('content')
<section class="blueprint-grid border-b border-brand-100 bg-white">
    <div class="container-app py-12 sm:py-16">
        <p class="eyebrow">Katalog layanan</p>
        <h1 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Pilih layanan yang Anda butuhkan</h1>
        <p class="mt-4 max-w-2xl text-slate-600">Semua harga dalam Rupiah. Estimasi durasi membantu Anda memilih layanan sesuai kebutuhan.</p>
    </div>
</section>

<section class="container-app py-10 sm:py-14">
    <div class="flex flex-wrap gap-2" aria-label="Filter kategori">
        <a href="{{ route('catalog.index') }}" class="rounded-lg border px-4 py-2 text-sm font-semibold transition {{ $selectedCategory === null ? 'border-brand-600 bg-brand-600 text-white shadow-sm' : 'border-slate-200 bg-white text-slate-600 hover:border-brand-300 hover:bg-brand-50' }}">Semua layanan</a>
        @foreach ($categories as $category)
            <a href="{{ route('catalog.index', ['category' => $category->slug]) }}" class="rounded-lg border px-4 py-2 text-sm font-semibold transition {{ $selectedCategory?->id === $category->id ? 'border-brand-600 bg-brand-600 text-white shadow-sm' : 'border-slate-200 bg-white text-slate-600 hover:border-brand-300 hover:bg-brand-50' }}">{{ $category->name }}</a>
        @endforeach
    </div>

    @if ($selectedCategory)
        <div class="mt-8 rounded-2xl border border-brand-200 bg-brand-50/80 p-5">
            <h2 class="font-semibold text-brand-900">{{ $selectedCategory->name }}</h2>
            <p class="mt-1 text-sm text-brand-800/80">{{ $selectedCategory->description }}</p>
        </div>
    @endif

    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($services as $service)
            <article class="card flex flex-col border-t-4 border-t-brand-500 p-6">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-brand-600">{{ $service->category?->name ?? 'Layanan' }}</p>
                        <h2 class="mt-2 text-lg font-semibold text-slate-900">{{ $service->name }}</h2>
                    </div>
                    <span class="rounded-lg bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-600">{{ $service->unit->label() }}</span>
                </div>
                @if ($service->description)
                    <p class="mt-3 text-sm leading-6 text-slate-500">{{ $service->description }}</p>
                @endif
                <div class="mt-auto border-t border-slate-100 pt-5">
                    <p class="text-2xl font-bold tracking-tight text-slate-900">Rp{{ number_format($service->price, 0, ',', '.') }}<span class="text-sm font-medium text-slate-500">/{{ $service->unit->value }}</span></p>
                    <p class="mt-1 text-xs text-slate-500">Estimasi {{ intdiv($service->duration_minutes, 60) }} jam</p>
                    @auth
                        @if (auth()->user()->isCustomer())
                            <a href="{{ route('customer.orders.create', ['service' => $service->id]) }}" class="btn-primary mt-4 w-full">Pesan layanan</a>
                        @elseif (auth()->user()->isStaff())
                            <a href="{{ route('admin.dashboard') }}" class="btn-secondary mt-4 w-full">Buka dashboard</a>
                        @endif
                    @else
                        <a href="{{ route('register') }}" class="btn-primary mt-4 w-full">Mulai pesan</a>
                    @endauth
                </div>
            </article>
        @empty
            <div class="card col-span-full p-10 text-center text-slate-500">Belum ada layanan aktif pada kategori ini.</div>
        @endforelse
    </div>
</section>
@endsection
