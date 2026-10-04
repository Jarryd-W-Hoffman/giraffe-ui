@props([
    'src' => null,
    'name' => null,
    'alt' => null,
    'size' => 'md',
])

@php
    $sizes = [
        'sm' => 'size-8 text-xs',
        'md' => 'size-10 text-sm',
        'lg' => 'size-12 text-base',
    ];

    $label = $alt ?? $name;

    // "Ada Lovelace" becomes "AL". Multibyte-safe.
    $initials = collect(preg_split('/\s+/u', trim((string) $name), -1, PREG_SPLIT_NO_EMPTY))
        ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
        ->take(2)
        ->implode('');

    $classes = 'inline-flex shrink-0 items-center justify-center overflow-hidden rounded-full '.($sizes[$size] ?? $sizes['md']);
@endphp

@if ($src)
    <img src="{{ $src }}" alt="{{ $label ?? '' }}" {{ $attributes->class([$classes, 'object-cover']) }}>
@else
    <span
        @if ($label) role="img" aria-label="{{ $label }}" @else aria-hidden="true" @endif
        {{ $attributes->class([$classes, 'bg-zinc-200 font-medium text-zinc-700 dark:bg-zinc-700 dark:text-zinc-100']) }}
    >
        @if ($initials !== '')
            <span aria-hidden="true">{{ $initials }}</span>
        @else
            <svg class="size-3/5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.23 1.23 0 0 0 .41 1.412A9.957 9.957 0 0 0 10 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 0 0-13.074.003Z" /></svg>
        @endif
    </span>
@endif
