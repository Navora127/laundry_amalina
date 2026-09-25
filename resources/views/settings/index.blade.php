@extends('layouts.app')

@section('title', 'Pengaturan akun — LaundryKu')

@section('content')
<section class="container-app py-10 sm:py-14">
    <div><p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Akun</p><h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Pengaturan</h1><p class="mt-2 text-slate-600">Kelola profil, password, dan keamanan akun.</p></div>
    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <div class="card p-6"><h2 class="text-lg font-semibold text-slate-900">Profil</h2><p class="mt-1 text-sm text-slate-500">Perubahan email memerlukan verifikasi ulang.</p><form method="POST" action="{{ route('user-profile-information.update') }}" class="mt-5 space-y-4">@csrf @method('PUT')<div><label for="name" class="form-label">Nama</label><input id="name" name="name" value="{{ old('name', $user->name) }}" class="form-input" required>@error('name')<p class="form-error">{{ $message }}</p>@enderror</div><div><label for="email" class="form-label">Email</label><input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" class="form-input" required>@error('email')<p class="form-error">{{ $message }}</p>@enderror</div><button class="btn-primary">Simpan profil</button></form></div>
        <div class="card p-6"><h2 class="text-lg font-semibold text-slate-900">Password</h2><p class="mt-1 text-sm text-slate-500">Gunakan password panjang dan unik.</p><form method="POST" action="{{ route('user-password.update') }}" class="mt-5 space-y-4">@csrf @method('PUT')<div><label for="current_password" class="form-label">Password saat ini</label><input id="current_password" name="current_password" type="password" class="form-input" required></div><div><label for="new_password" class="form-label">Password baru</label><input id="new_password" name="password" type="password" class="form-input" required></div><div><label for="password_confirmation" class="form-label">Ulangi password baru</label><input id="password_confirmation" name="password_confirmation" type="password" class="form-input" required></div><button class="btn-primary">Ubah password</button></form></div>
        <div class="card p-6 lg:col-span-2"><div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between"><div><h2 class="text-lg font-semibold text-slate-900">Keamanan dua faktor</h2><p class="mt-1 text-sm text-slate-500">Tambahkan kode autentikator untuk melindungi akun staff.</p></div><span class="badge {{ $user->two_factor_confirmed_at ? 'bg-emerald-100 text-emerald-800 ring-emerald-200' : 'bg-amber-100 text-amber-800 ring-amber-200' }}">{{ $user->two_factor_confirmed_at ? 'Aktif' : 'Belum aktif' }}</span></div>
            @if ($user->two_factor_confirmed_at)
                <form method="POST" action="{{ route('two-factor.disable') }}" class="mt-5" onsubmit="return confirm('Matikan verifikasi dua faktor?')">@csrf @method('DELETE')<button class="btn-danger">Matikan dua faktor</button></form>
            @elseif ($user->two_factor_secret)
                <div class="mt-5 grid gap-5 sm:grid-cols-[180px_1fr]"><div class="rounded-xl bg-white p-3 ring-1 ring-slate-200"><img src="{{ route('two-factor.qr-code') }}" alt="QR code two factor" class="h-auto w-full"></div><div><p class="text-sm font-medium text-slate-800">Pindai QR code, lalu masukkan kode dari aplikasi autentikator.</p><p class="mt-2 text-xs text-slate-500">Jika QR code sulit dibaca, <a href="{{ route('two-factor.secret-key') }}" target="_blank" class="font-semibold text-brand-700">buka secret key</a>.</p><form method="POST" action="{{ route('two-factor.confirm') }}" class="mt-4 flex gap-2">@csrf<input name="code" inputmode="numeric" class="form-input" placeholder="6 digit kode" required><button class="btn-primary">Konfirmasi</button></form></div></div>
            @else
                <form method="POST" action="{{ route('two-factor.enable') }}" class="mt-5">@csrf<button class="btn-primary">Aktifkan dua faktor</button></form>
            @endif
        </div>
        <div class="card p-6 lg:col-span-2"><h2 class="text-lg font-semibold text-slate-900">Passkey</h2><p class="mt-1 text-sm text-slate-500">Passkey yang terdaftar: <strong>{{ $user->passkeys->count() }}</strong>.</p><p class="mt-3 text-xs text-slate-400">Pengelolaan passkey membutuhkan browser yang mendukung WebAuthn dan koneksi HTTPS.</p></div>
    </div>
</section>
@endsection
