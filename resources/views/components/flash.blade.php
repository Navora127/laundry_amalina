@if (session('success'))
    <div class="container-app pt-5">
        <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
            <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-xs text-white">✓</span>
            <p>{{ session('success') }}</p>
        </div>
    </div>
@endif

@if (session('error'))
    <div class="container-app pt-5">
        <div class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">
            <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-rose-600 text-xs text-white">!</span>
            <p>{{ session('error') }}</p>
        </div>
    </div>
@endif

@if ($errors->any())
    <div class="container-app pt-5">
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">
            <p class="font-semibold">Periksa kembali data yang Anda masukkan.</p>
            <ul class="mt-1 list-inside list-disc text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
