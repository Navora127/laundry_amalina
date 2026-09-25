@extends('layouts.auth')

@section('title', 'Verifikasi email')

@section('content')
<div class="mb-8">
    <p class="text-sm font-semibold text-brand-600">Satu langkah lagi</p>
    <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Verifikasi email Anda</h1>
    <p class="mt-2 text-sm text-slate-500">Kami telah mengirim tautan verifikasi ke {{ auth()->user()->email }}.</p>
</div>

@if (session('status') === 'verification-link-sent')
    <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">Tautan verifikasi baru telah dikirim.</div>
@endif

<div class="flex flex-col gap-3 sm:flex-row">
    <form method="POST" action="{{ route('verification.send') }}" class="flex-1">
        @csrf
        <button type="submit" class="btn-primary w-full">Kirim ulang verifikasi</button>
    </form>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-secondary w-full">Keluar</button>
    </form>
</div>
@endsection
