@extends('layouts.app')

@section('title', 'Pesan laundry — LaundryKu')

@section('content')
<section class="container-app py-10 sm:py-14">
    <div class="mx-auto max-w-4xl">
        <a href="{{ route('customer.orders.index') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">← Kembali ke pesanan</a>
        <div class="mt-5"><p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Checkout</p><h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Buat pesanan laundry</h1><p class="mt-2 text-slate-600">Pilih layanan, tentukan pengambilan, dan sistem akan menghitung estimasi harga.</p></div>

        @if ($addresses->isEmpty())
            <div class="mt-8 rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-800">Tambahkan alamat terlebih dahulu sebelum membuat pesanan. <a href="{{ route('customer.addresses.index') }}" class="font-semibold underline">Kelola alamat</a></div>
        @else
            <form method="POST" action="{{ route('customer.orders.store') }}" class="mt-8 space-y-6" x-data="{ rows: [], add(serviceId, price, unit, selected = false) { if (selected) { const existing = this.rows.find(row => row.service_id === serviceId); if (existing) { existing.qty = Math.min(9999.99, Number(existing.qty) + 1); return; } } this.rows.push({ service_id: serviceId, price: Number(price), unit: unit, qty: 1 }); }, remove(index) { this.rows.splice(index, 1); }, format(value) { return new Intl.NumberFormat('id-ID').format(value); } }">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ \Illuminate\Support\Str::uuid() }}">

                <div class="card p-6">
                    <div class="flex items-start justify-between gap-4"><div><h2 class="text-lg font-semibold text-slate-900">1. Pilih layanan</h2><p class="mt-1 text-sm text-slate-500">Klik layanan untuk menambahkan ke pesanan.</p></div><span class="rounded-lg bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700">Harga transparan</span></div>
                    <div class="mt-6 space-y-6">
                        @forelse ($categories as $category)
                            <div>
                                <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-500">{{ $category->name }}</h3>
                                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                    @foreach ($category->services as $service)
                                        <button type="button" @click="add({{ $service->id }}, {{ $service->price }}, '{{ $service->unit->value }}', {{ (int) $selectedServiceId === $service->id ? 'true' : 'false' }})" class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 p-4 text-left transition hover:border-brand-400 hover:bg-brand-50">
                                            <span><span class="block text-sm font-semibold text-slate-800">{{ $service->name }}</span><span class="mt-1 block text-xs text-slate-500">Rp{{ number_format($service->price, 0, ',', '.') }}/{{ $service->unit->value }}</span></span><span class="text-lg text-brand-600">+</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">Belum ada layanan aktif.</p>
                        @endforelse
                    </div>
                    @error('items')<p class="form-error mt-3">{{ $message }}</p>@enderror
                </div>

                <div class="card p-6" x-show="rows.length > 0" x-cloak>
                    <h2 class="text-lg font-semibold text-slate-900">Rincian pesanan</h2>
                    <div class="mt-4 space-y-3">
                        <template x-for="(row, index) in rows" :key="row.service_id">
                            <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-3">
                                <input type="hidden" :name="`items[${index}][service_id]`" :value="row.service_id">
                                <div class="min-w-0 flex-1"><p class="truncate text-sm font-semibold text-slate-800" x-text="`Layanan #${row.service_id}`"></p><p class="text-xs text-slate-500">Rp<span x-text="format(row.price)"></span>/<span x-text="row.unit"></span></p></div>
                                <label class="sr-only" :for="`qty-${row.service_id}`">Jumlah</label>
                                <input :id="`qty-${row.service_id}`" x-model.number="row.qty" type="number" min="0.01" max="9999.99" step="0.01" class="form-input w-24" :name="`items[${index}][qty]`">
                                <p class="w-28 text-right text-sm font-semibold text-slate-900">Rp<span x-text="format(row.price * row.qty)"></span></p>
                                <button type="button" @click="remove(index)" class="text-slate-400 hover:text-rose-600" aria-label="Hapus layanan">×</button>
                            </div>
                        </template>
                    </div>
                    <p class="mt-4 text-right text-lg font-bold text-brand-700">Estimasi: Rp<span x-text="format(rows.reduce((total, row) => total + (row.price * row.qty), 0))"></span></p>
                </div>

                <div class="card p-6">
                    <h2 class="text-lg font-semibold text-slate-900">2. Pengambilan dan alamat</h2>
                    <div class="mt-5 grid gap-5 sm:grid-cols-2">
                        <div><label for="fulfillment_method" class="form-label">Metode</label><select id="fulfillment_method" name="fulfillment_method" class="form-input" required><option value="pickup">Ambil sendiri</option><option value="delivery">Antar</option></select>@error('fulfillment_method')<p class="form-error">{{ $message }}</p>@enderror</div>
                        <div><label for="scheduled_at" class="form-label">Jadwal (opsional)</label><input id="scheduled_at" name="scheduled_at" type="datetime-local" class="form-input">@error('scheduled_at')<p class="form-error">{{ $message }}</p>@enderror</div>
                        <div class="sm:col-span-2"><label for="address_id" class="form-label">Alamat</label><select id="address_id" name="address_id" class="form-input" required><option value="">Pilih alamat</option>@foreach ($addresses as $address)<option value="{{ $address->id }}" @selected(old('address_id') == $address->id)>{{ $address->label }} — {{ $address->recipient }}, {{ $address->city }}{{ $address->is_default ? ' (default)' : '' }}</option>@endforeach</select>@error('address_id')<p class="form-error">{{ $message }}</p>@enderror</div>
                        <div class="sm:col-span-2"><label for="notes" class="form-label">Catatan (opsional)</label><textarea id="notes" name="notes" rows="3" class="form-input" placeholder="Contoh: warna sensitif, barang berharga, dll.">{{ old('notes') }}</textarea>@error('notes')<p class="form-error">{{ $message }}</p>@enderror</div>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"><a href="{{ route('customer.orders.index') }}" class="btn-secondary">Batal</a><button type="submit" class="btn-primary" x-bind:disabled="rows.length === 0">Buat pesanan</button></div>
            </form>
        @endif
    </div>
</section>
@endsection
