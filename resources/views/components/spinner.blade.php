@props([
    'size' => 'md',
    'label' => null,
])

@php
    $sizes = [
        'sm' => 'size-4',
        'md' => 'size-5',
        'lg' => 'size-8',
    ];
@endphp

{{-- With a label it's announced as a status; without one it's decorative (e.g. inside a button). --}}
<span
    @if ($label) role="status" @else aria-hidden="true" @endif
    {{ $attributes->class('inline-flex shrink-0 items-center justify-center') }}
>
    <svg class="{{ $sizes[$size] ?? $sizes['md'] }} animate-spin motion-reduce:animate-[spin_1.5s_linear_infinite]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <circle cx="12" cy="12" r="9.5" stroke="currentColor" stroke-width="3" class="opacity-25" />
        <path d="M21.5 12A9.5 9.5 0 0 0 12 2.5" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
    </svg>
    @if ($label)
        <span class="sr-only">{{ $label }}</span>
    @endif
</span>
