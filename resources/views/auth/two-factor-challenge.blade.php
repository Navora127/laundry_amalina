@extends('layouts.auth')

@section('title', 'Verifikasi dua faktor')

@section('content')
<div class="mb-8">
    <p class="text-sm font-semibold text-brand-600">Keamanan tambahan</p>
    <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Verifikasi dua faktor</h1>
    <p class="mt-2 text-sm text-slate-500">Masukkan kode dari aplikasi autentikator atau recovery code.</p>
</div>

<form method="POST" action="{{ route('two-factor.login') }}" class="space-y-5">
    @csrf
    <div>
        <label for="code" class="form-label">Kode autentikator</label>
        <input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" autofocus class="form-input text-center text-xl tracking-[0.4em]" placeholder="000000">
        @error('code')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="recovery_code" class="form-label">Recovery code (opsional)</label>
        <input id="recovery_code" name="recovery_code" type="text" autocomplete="one-time-code" class="form-input" placeholder="Kode pemulihan">
        @error('recovery_code')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <button type="submit" class="btn-primary w-full">Verifikasi</button>
</form>
@endsection
