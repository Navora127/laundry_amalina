@props(['title', 'description'])

<div class="card flex flex-col items-center justify-center px-6 py-14 text-center">
    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-2xl text-brand-600">✦</div>
    <h3 class="text-base font-semibold text-slate-900">{{ $title }}</h3>
    <p class="mt-1 max-w-md text-sm text-slate-500">{{ $description }}</p>
    @if (! $slot->isEmpty())
        <div class="mt-5">{{ $slot }}</div>
    @endif
</div>
