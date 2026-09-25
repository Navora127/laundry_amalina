@extends('layouts.auth')

@section('title', 'Lupa password')

@section('content')
<div class="mb-8">
    <p class="text-sm font-semibold text-brand-600">Pemulihan akun</p>
    <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Lupa password?</h1>
    <p class="mt-2 text-sm text-slate-500">Masukkan email akun. Kami akan mengirim tautan untuk mengatur password baru.</p>
</div>

@if (session('status'))
    <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('password.email') }}" class="space-y-5">
    @csrf
    <div>
        <label for="email" class="form-label">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus class="form-input" placeholder="nama@email.com">
        @error('email')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <button type="submit" class="btn-primary w-full">Kirim tautan reset</button>
</form>

<p class="mt-6 text-center text-sm"><a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:text-brand-800">Kembali ke halaman masuk</a></p>
@endsection
