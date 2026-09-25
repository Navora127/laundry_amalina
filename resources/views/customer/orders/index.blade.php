@extends('layouts.app')

@section('title', 'Pesanan saya — LaundryKu')

@section('content')
<section class="container-app py-10 sm:py-14">
    <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
        <div><p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Portal pelanggan</p><h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Pesanan saya</h1><p class="mt-2 text-slate-600">Semua riwayat dan status laundry Anda.</p></div>
        <a href="{{ route('customer.orders.create') }}" class="btn-primary">+ Pesan laundry</a>
    </div>

    <div class="card mt-8 overflow-hidden">
        @if ($orders->isEmpty())
            <div class="p-8">
                <x-empty-state title="Belum ada pesanan" description="Pesanan laundry Anda akan tampil di sini setelah checkout.">
                    <a href="{{ route('customer.orders.create') }}" class="btn-primary">Mulai pesanan</a>
                </x-empty-state>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($orders as $order)
                    <a href="{{ route('customer.orders.show', $order) }}" class="flex flex-col gap-4 p-5 transition hover:bg-slate-50 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-4"><span class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 font-bold text-brand-700">L</span><div><p class="font-semibold text-slate-900">{{ $order->code }}</p><p class="mt-1 text-xs text-slate-500">{{ $order->created_at->format('d M Y, H:i') }} · {{ $order->fulfillment_method->label() }}</p></div></div>
                        <div class="flex items-center justify-between gap-4 sm:justify-end"><x-status-badge :status="$order->status" /><span class="text-sm font-semibold text-slate-900">Rp{{ number_format($order->final_total, 0, ',', '.') }}</span></div>
                    </a>
                @endforeach
            </div>
            <div class="border-t border-slate-100 px-5 py-4">{{ $orders->links() }}</div>
        @endif
    </div>
</section>
@endsection
