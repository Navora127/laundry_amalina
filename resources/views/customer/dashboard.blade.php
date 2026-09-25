@extends('layouts.app')

@section('title', 'Dashboard pelanggan — LaundryKu')

@section('content')
<section class="container-app py-10 sm:py-14">
    <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Portal pelanggan</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Halo, {{ auth()->user()->name }} 👋</h1>
            <p class="mt-2 text-slate-600">Pantau laundry Anda atau buat pesanan baru.</p>
        </div>
        <a href="{{ route('customer.orders.create') }}" class="btn-primary">+ Pesan laundry</a>
    </div>

    <div class="mt-8 grid gap-4 sm:grid-cols-3">
        <div class="card p-5"><p class="text-sm text-slate-500">Total pesanan</p><p class="mt-2 text-3xl font-bold text-slate-900">{{ auth()->user()->customer?->orders()->count() ?? 0 }}</p></div>
        <div class="card p-5"><p class="text-sm text-slate-500">Sedang diproses</p><p class="mt-2 text-3xl font-bold text-brand-600">{{ auth()->user()->customer?->orders()->whereIn('status', ['awaiting_pickup', 'processing'])->count() ?? 0 }}</p></div>
        <div class="card p-5"><p class="text-sm text-slate-500">Siap diambil</p><p class="mt-2 text-3xl font-bold text-cyan-600">{{ auth()->user()->customer?->orders()->where('status', 'ready_for_pickup')->count() ?? 0 }}</p></div>
    </div>

    <div class="mt-10 flex items-center justify-between gap-4">
        <div><h2 class="text-xl font-bold text-slate-900">Pesanan terbaru</h2><p class="mt-1 text-sm text-slate-500">Status terakhir pesanan Anda.</p></div>
        <a href="{{ route('customer.orders.index') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">Lihat semua →</a>
    </div>

    <div class="mt-5 space-y-3">
        @forelse ($orders as $order)
            <a href="{{ route('customer.orders.show', $order) }}" class="card flex flex-col gap-4 p-5 transition hover:border-brand-200 hover:shadow-md sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-lg text-brand-700">L</span>
                    <div><p class="font-semibold text-slate-900">{{ $order->code }}</p><p class="mt-1 text-xs text-slate-500">{{ $order->created_at->format('d M Y, H:i') }} · {{ $order->items->count() }} layanan</p></div>
                </div>
                <div class="flex items-center justify-between gap-5 sm:justify-end"><x-status-badge :status="$order->status" /><span class="font-semibold text-slate-900">Rp{{ number_format($order->final_total, 0, ',', '.') }}</span></div>
            </a>
        @empty
            <x-empty-state title="Belum ada pesanan" description="Pesanan laundry Anda akan tampil di sini setelah checkout.">
                <a href="{{ route('customer.orders.create') }}" class="btn-primary">Mulai pesanan</a>
            </x-empty-state>
        @endforelse
    </div>
</section>
@endsection
