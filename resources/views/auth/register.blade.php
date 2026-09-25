@extends('layouts.auth')

@section('title', 'Daftar')

@section('content')
<div class="mb-8">
    <p class="text-sm font-semibold text-brand-600">Mulai lebih mudah</p>
    <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Buat akun pelanggan</h1>
    <p class="mt-2 text-sm text-slate-500">Satu akun untuk order, alamat, dan tracking laundry.</p>
</div>

@include('components.flash')

<form method="POST" action="{{ route('register') }}" class="space-y-4">
    @csrf
    <div>
        <label for="name" class="form-label">Nama lengkap</label>
        <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required autofocus class="form-input" placeholder="Nama Anda">
        @error('name')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="email" class="form-label">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required class="form-input" placeholder="nama@email.com">
        @error('email')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="phone" class="form-label">Nomor WhatsApp</label>
        <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel" required class="form-input" placeholder="08xxxxxxxxxx">
        @error('phone')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="password" class="form-label">Password</label>
        <input id="password" name="password" type="password" autocomplete="new-password" required class="form-input" placeholder="Minimal 10 karakter">
        @error('password')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="password_confirmation" class="form-label">Ulangi password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="form-input" placeholder="Ketik ulang password">
    </div>
    <button type="submit" class="btn-primary mt-2 w-full">Buat akun</button>
</form>

<p class="mt-6 text-center text-sm text-slate-500">Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:text-brand-800">Masuk</a></p>
@endsection
