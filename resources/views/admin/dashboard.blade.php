@extends('layouts.app')

@section('title', 'Dashboard admin — LaundryKu')

@section('content')
<section class="container-app py-10 sm:py-14">
    <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Operasional laundry</p><h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Dashboard admin</h1><p class="mt-2 text-slate-600">Pantau pesanan, proses, dan pendapatan toko.</p></div><div class="flex flex-wrap gap-2"><a href="{{ route('admin.catalog.services') }}" class="btn-secondary">Katalog</a><a href="{{ route('admin.reports.index') }}" class="btn-secondary">Laporan</a><a href="{{ route('admin.orders.walk-in.create') }}" class="btn-primary">+ Input laundry manual</a><a href="{{ route('admin.orders.index') }}" class="btn-primary">Kelola pesanan</a></div></div>
    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="card p-5"><p class="text-sm text-slate-500">Pendapatan hari ini</p><p class="mt-2 text-2xl font-bold text-brand-700">Rp{{ number_format($stats['today_revenue'], 0, ',', '.') }}</p><p class="mt-1 text-xs text-slate-400">Pembayaran terverifikasi</p></div>
        <div class="card p-5"><p class="text-sm text-slate-500">Perlu tindakan</p><p class="mt-2 text-2xl font-bold text-amber-600">{{ $stats['pending_orders'] }}</p><p class="mt-1 text-xs text-slate-400">Konfirmasi dan penjemputan</p></div>
        <div class="card p-5"><p class="text-sm text-slate-500">Sedang diproses</p><p class="mt-2 text-2xl font-bold text-blue-600">{{ $stats['processing_orders'] }}</p><p class="mt-1 text-xs text-slate-400">Cuci dan setrika</p></div>
        <div class="card p-5"><p class="text-sm text-slate-500">Perlu diambil</p><p class="mt-2 text-2xl font-bold text-cyan-600">{{ $stats['uncollected_orders'] }}</p><p class="mt-1 text-xs text-slate-400">Lebih dari 3 hari</p></div>
    </div>
    <div class="mt-10 flex items-center justify-between gap-4"><div><h2 class="text-xl font-bold text-slate-900">Pesanan terbaru</h2><p class="mt-1 text-sm text-slate-500">Aktivitas order masuk dari pelanggan dan kasir.</p></div><a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-brand-700">Lihat semua →</a></div>
    <div class="card mt-5 overflow-hidden">
        @if ($recentOrders->isEmpty())
            <div class="p-8"><x-empty-state title="Belum ada pesanan" description="Pesanan baru akan muncul di sini." /></div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($recentOrders as $order)
                    <a href="{{ route('admin.orders.show', $order) }}" class="flex flex-col gap-3 p-5 transition hover:bg-slate-50 sm:flex-row sm:items-center sm:justify-between"><div><p class="font-semibold text-slate-900">{{ $order->code }} <span class="ml-2 text-xs font-normal text-slate-400">{{ $order->customer_name }}</span></p><p class="mt-1 text-xs text-slate-500">{{ $order->created_at->format('d M Y, H:i') }} · {{ $order->source->label() }}</p></div><div class="flex items-center gap-4"><x-status-badge :status="$order->status" /><span class="text-sm font-semibold text-slate-900">Rp{{ number_format($order->final_total, 0, ',', '.') }}</span></div></a>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
