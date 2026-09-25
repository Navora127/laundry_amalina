@props(['status'])

@php
    $statusValue = $status instanceof \App\Enums\OrderStatus ? $status : \App\Enums\OrderStatus::from($status);
@endphp

<span {{ $attributes->merge(['class' => 'badge '.$statusValue->badgeClass()]) }}>{{ $statusValue->label() }}</span>
