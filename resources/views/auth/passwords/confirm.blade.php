@extends('layouts.auth')

@section('title', 'Konfirmasi password')

@section('content')
<div class="mb-8">
    <p class="text-sm font-semibold text-brand-600">Konfirmasi keamanan</p>
    <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Masukkan password Anda</h1>
    <p class="mt-2 text-sm text-slate-500">Konfirmasi password sebelum melanjutkan ke pengaturan sensitif.</p>
</div>

<form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
    @csrf
    <div>
        <label for="password" class="form-label">Password</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required autofocus class="form-input">
        @error('password')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <button type="submit" class="btn-primary w-full">Konfirmasi</button>
</form>
@endsection
