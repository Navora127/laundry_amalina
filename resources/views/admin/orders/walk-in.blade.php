@extends('layouts.app')

@section('title', 'Order walk-in — Admin LaundryKu')

@section('content')
<section class="container-app py-10 sm:py-14">
    <div class="mx-auto max-w-4xl">
        <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">&larr; Kembali ke pesanan</a>

        <div class="mt-5">
            <p class="eyebrow">Transaksi toko</p>
            <h1 class="mt-3 text-3xl font-bold tracking-tight text-slate-900">Input laundry manual</h1>
            <p class="mt-2 text-slate-600">
                Untuk pelanggan yang datang langsung ke toko, tanpa akun dan tanpa order lewat aplikasi.
            </p>
        </div>

        <form
            method="POST"
            action="{{ route('admin.orders.walk-in.store') }}"
            class="mt-8 space-y-6"
            x-data="{
                rows: [],
                add(serviceId, name, price, unit) {
                    const existing = this.rows.find(row => row.service_id === serviceId);
                    if (existing) {
                        existing.qty = Math.min(9999.99, Number(existing.qty) + 1);
                        return;
                    }
                    this.rows.push({ service_id: serviceId, name: name, price: Number(price), unit: unit, qty: 1 });
                },
                setQty(index, value) {
                    this.rows[index].qty = Math.max(0.01, Math.min(9999.99, Number(value) || 0.01));
                },
                remove(index) {
                    this.rows.splice(index, 1);
                },
                unitLabel(unit) {
                    return unit === 'kg' ? 'kg' : 'pcs';
                },
                format(value) {
                    return new Intl.NumberFormat('id-ID').format(value);
                },
                get total() {
                    return this.rows.reduce((sum, row) => sum + row.price * row.qty, 0);
                },
            }"
        >
            @csrf

            <div class="card p-6">
                <h2 class="text-lg font-semibold text-slate-900">Data pelanggan</h2>
                <p class="mt-1 text-sm text-slate-500">Nama dan nomor WhatsApp wajib diisi.</p>

                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="customer_name" class="form-label">Nama</label>
                        <input id="customer_name" name="customer_name" value="{{ old('customer_name') }}" class="form-input" required>
                        @error('customer_name')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="customer_phone" class="form-label">Nomor WhatsApp</label>
                        <input id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" class="form-input" placeholder="08xxxxxxxxxx" required>
                        @error('customer_phone')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="customer_email" class="form-label">Email (opsional)</label>
                        <input id="customer_email" name="customer_email" type="email" value="{{ old('customer_email') }}" class="form-input">
                        @error('customer_email')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="fulfillment_method" class="form-label">Metode</label>
                        <select id="fulfillment_method" name="fulfillment_method" class="form-input">
                            <option value="pickup">Ambil sendiri di toko</option>
                        </select>
                        <input type="hidden" name="address_id" value="">
                        @error('fulfillment_method')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <h2 class="text-lg font-semibold text-slate-900">Layanan &amp; berat</h2>
                <p class="mt-1 text-sm text-slate-500">Klik layanan, lalu ubah jumlahnya sesuai berat atau jumlah satuan.</p>

                <div class="mt-5 space-y-5">
                    @foreach ($categories as $category)
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">{{ $category->name }}</h3>
                            <div class="mt-2 grid gap-2 sm:grid-cols-2">
                                @foreach ($category->services as $service)
                                    <button
                                        type="button"
                                        @click="add({{ $service->id }}, @js($service->name), {{ $service->price }}, '{{ $service->unit->value }}')"
                                        class="flex items-center justify-between gap-3 rounded-lg border border-slate-200 p-3 text-left transition hover:border-brand-400 hover:bg-brand-50"
                                    >
                                        <span class="min-w-0">
                                            <span class="block truncate text-sm font-semibold text-slate-800">{{ $service->name }}</span>
                                            <span class="mt-0.5 block text-xs text-slate-500">
                                                Rp{{ number_format($service->price, 0, ',', '.') }} / {{ $service->unit->value }}
                                            </span>
                                        </span>
                                        <span class="shrink-0 text-xs font-semibold text-brand-700">+ Tambah</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                @error('items')<p class="form-error mt-3">{{ $message }}</p>@enderror
                @error('items.*')<p class="form-error mt-3">{{ $message }}</p>@enderror

                <div class="mt-6 space-y-3" x-show="rows.length > 0" x-cloak>
                    <template x-for="(row, index) in rows" :key="row.service_id">
                        <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-slate-900" x-text="row.name"></p>
                                    <p class="mt-0.5 text-xs text-slate-500">
                                        Rp<span x-text="format(row.price)"></span> / <span x-text="unitLabel(row.unit)"></span>
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    @click="remove(index)"
                                    class="shrink-0 rounded-lg px-2 py-1 text-xs font-semibold text-slate-500 transition hover:bg-rose-50 hover:text-rose-700"
                                >
                                    Hapus
                                </button>
                            </div>

                            <div class="mt-3 flex flex-wrap items-center gap-3">
                                <label :for="`qty-${index}`" class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    <span x-text="row.unit === 'kg' ? 'Berat (kg)' : 'Jumlah (pcs)'"></span>
                                </label>
                                <input
                                    :id="`qty-${index}`"
                                    :name="`items[${index}][qty]`"
                                    type="number"
                                    min="0.01"
                                    max="9999.99"
                                    step="0.01"
                                    inputmode="decimal"
                                    x-model.number="row.qty"
                                    class="form-input w-28"
                                    required
                                >
                                <input type="hidden" :name="`items[${index}][service_id]`" :value="row.service_id">

                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="preset in [0.5, 1, 2, 3, 5]" :key="preset">
                                        <button
                                            type="button"
                                            @click="setQty(index, preset)"
                                            class="rounded-lg border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 transition hover:border-brand-400 hover:bg-brand-50 hover:text-brand-800"
                                            x-text="preset"
                                        ></button>
                                    </template>
                                </div>

                                <p class="ml-auto font-bold text-brand-700">
                                    Rp<span x-text="format(row.price * row.qty)"></span>
                                </p>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-5 flex items-center justify-between rounded-xl bg-brand-50 px-4 py-3" x-show="rows.length > 0" x-cloak>
                    <span class="text-sm font-semibold text-brand-900">Total laundry</span>
                    <span class="text-lg font-bold text-brand-800">Rp<span x-text="format(total)"></span></span>
                </div>
            </div>

            <div class="card p-6">
                <label for="notes" class="form-label">Catatan (opsional)</label>
                <textarea id="notes" name="notes" rows="3" class="form-input" placeholder="Contoh:encies khusus, tidak setrika, selesai sebelum jam 5 sore.">{{ old('notes') }}</textarea>
                @error('notes')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.orders.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary" x-bind:disabled="rows.length === 0">
                    Simpan order walk-in
                </button>
            </div>
        </form>
    </div>
</section>
@endsection