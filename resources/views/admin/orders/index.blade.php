@extends('layouts.app')

@section('title', 'Kelola pesanan — LaundryKu')

@section('content')
<section class="container-app py-10 sm:py-14">
    <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Operasional laundry</p><h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Kelola pesanan</h1><p class="mt-2 text-slate-600">Verifikasi harga, ubah status, dan pantau pesanan pelanggan.</p></div><a href="{{ route('catalog.index') }}" class="btn-secondary">Lihat katalog</a></div>
    <form method="GET" action="{{ route('admin.orders.index') }}" class="card mt-8 grid gap-3 p-4 sm:grid-cols-[1fr_220px_auto]"><div><label class="sr-only" for="search">Cari pesanan</label><input id="search" name="search" value="{{ $search }}" class="form-input" placeholder="Cari kode, nama, atau nomor telepon"></div><div><label class="sr-only" for="status">Status</label><select id="status" name="status" class="form-input"><option value="">Semua status</option>@foreach ($statuses as $statusOption)<option value="{{ $statusOption->value }}" @selected($selectedStatus === $statusOption->value)>{{ $statusOption->label() }}</option>@endforeach</select></div><button class="btn-primary">Filter</button></form>
    <div class="card mt-6 overflow-hidden">
        @if ($orders->isEmpty())
            <div class="p-8"><x-empty-state title="Pesanan tidak ditemukan" description="Coba ubah filter atau buat pesanan walk-in dari sistem." /></div>
        @else
            <div class="hidden overflow-x-auto md:block"><table class="min-w-full divide-y divide-slate-100 text-left text-sm"><thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-5 py-3 font-semibold">Kode</th><th class="px-5 py-3 font-semibold">Pelanggan</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 font-semibold">Total</th><th class="px-5 py-3 font-semibold">Dibuat</th><th class="px-5 py-3"></th></tr></thead><tbody class="divide-y divide-slate-100">@foreach ($orders as $order)<tr class="hover:bg-slate-50"><td class="whitespace-nowrap px-5 py-4 font-semibold text-slate-900"><a href="{{ route('admin.orders.show', $order) }}" class="hover:text-brand-700">{{ $order->code }}</a><p class="mt-1 text-xs font-normal text-slate-400">{{ $order->source->label() }}</p></td><td class="px-5 py-4"><p class="font-medium text-slate-800">{{ $order->customer_name }}</p><p class="text-xs text-slate-500">{{ $order->customer_phone_e164 }}</p></td><td class="px-5 py-4"><x-status-badge :status="$order->status" /></td><td class="whitespace-nowrap px-5 py-4 font-semibold text-slate-900">Rp{{ number_format($order->final_total, 0, ',', '.') }}</td><td class="whitespace-nowrap px-5 py-4 text-slate-500">{{ $order->created_at->format('d M Y H:i') }}</td><td class="px-5 py-4 text-right"><a href="{{ route('admin.orders.show', $order) }}" class="font-semibold text-brand-700">Detail →</a></td></tr>@endforeach</tbody></table></div>
            <div class="divide-y divide-slate-100 md:hidden">@foreach ($orders as $order)<a href="{{ route('admin.orders.show', $order) }}" class="block p-5"><div class="flex items-start justify-between gap-3"><div><p class="font-semibold text-slate-900">{{ $order->code }}</p><p class="mt-1 text-xs text-slate-500">{{ $order->customer_name }} · {{ $order->created_at->format('d M Y H:i') }}</p></div><x-status-badge :status="$order->status" /></div><p class="mt-3 text-sm font-semibold text-slate-900">Rp{{ number_format($order->final_total, 0, ',', '.') }}</p></a>@endforeach</div>
            <div class="border-t border-slate-100 px-5 py-4">{{ $orders->links() }}</div>
        @endif
    </div>
</section>
@endsection
