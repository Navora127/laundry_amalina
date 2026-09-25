@extends('layouts.auth')

@section('title', 'Atur password baru')

@section('content')
<div class="mb-8">
    <p class="text-sm font-semibold text-brand-600">Keamanan akun</p>
    <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Atur password baru</h1>
    <p class="mt-2 text-sm text-slate-500">Gunakan password yang panjang dan unik.</p>
</div>

<form method="POST" action="{{ route('password.update') }}" class="space-y-5">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">
    <div>
        <label for="email" class="form-label">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $request->email) }}" autocomplete="email" required class="form-input" readonly>
        @error('email')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="password" class="form-label">Password baru</label>
        <input id="password" name="password" type="password" autocomplete="new-password" required autofocus class="form-input">
        @error('password')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="password_confirmation" class="form-label">Ulangi password baru</label>
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="form-input">
    </div>
    <button type="submit" class="btn-primary w-full">Simpan password</button>
</form>
@endsection
