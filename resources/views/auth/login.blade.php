@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')
<div class="mb-8">
    <p class="text-sm font-semibold text-brand-600">Selamat datang kembali</p>
    <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Masuk ke akun Anda</h1>
    <p class="mt-2 text-sm text-slate-500">Pantau laundry dan kelola pesanan dengan mudah.</p>
</div>

@include('components.flash')

<form method="POST" action="{{ route('login') }}" class="space-y-5">
    @csrf
    <div>
        <label for="email" class="form-label">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus class="form-input" placeholder="nama@email.com">
        @error('email')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <div class="flex items-center justify-between gap-3">
            <label for="password" class="form-label">Password</label>
            <a href="{{ route('password.request') }}" class="text-xs font-semibold text-brand-700 hover:text-brand-800">Lupa password?</a>
        </div>
        <input id="password" name="password" type="password" autocomplete="current-password" required class="form-input" placeholder="Masukkan password">
        @error('password')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <label class="flex items-center gap-2 text-sm text-slate-600">
        <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500" @checked(old('remember'))>
        Ingat saya
    </label>
    <button type="submit" class="btn-primary w-full">Masuk</button>
</form>

<p class="mt-6 text-center text-sm text-slate-500">Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-brand-700 hover:text-brand-800">Daftar sekarang</a></p>
@endsection
