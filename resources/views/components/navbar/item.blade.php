@props([
    'href',
    'current' => null,
])

@php
    // Without a current prop, an item is current when its URL is the page being shown.
    $current ??= url($href) === url()->current();
@endphp

{{-- Rendered in the bar and in the small-screen menu; the list it's in says which (data-place). --}}
<li>
    <a
        href="{{ $href }}"
        @if ($current) aria-current="page" @endif
        {{ $attributes->class([
            'flex items-center gap-2 rounded-[calc(var(--radius-gui)-2px)] px-3 py-2 text-sm font-medium text-zinc-600 transition-colors',
            'hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100',
            'aria-[current=page]:text-zinc-900 dark:aria-[current=page]:text-zinc-100',
            'group-data-[place=bar]/navbar:aria-[current=page]:bg-zinc-100 dark:group-data-[place=bar]/navbar:aria-[current=page]:bg-zinc-800',
            'group-data-[place=menu]/navbar:w-full group-data-[place=menu]/navbar:py-2.5 group-data-[place=menu]/navbar:text-base group-data-[place=menu]/navbar:aria-[current=page]:bg-zinc-100 dark:group-data-[place=menu]/navbar:aria-[current=page]:bg-zinc-800',
            'focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-gui-primary-text',
            'group-data-[variant=pills]/navbar:rounded-full group-data-[variant=pills]/navbar:bg-zinc-100 group-data-[variant=pills]/navbar:px-4 dark:group-data-[variant=pills]/navbar:bg-zinc-900',
            'group-data-[variant=pills]/navbar:aria-[current=page]:bg-zinc-900 group-data-[variant=pills]/navbar:aria-[current=page]:text-white dark:group-data-[variant=pills]/navbar:aria-[current=page]:bg-white dark:group-data-[variant=pills]/navbar:aria-[current=page]:text-zinc-900',
        ]) }}
    >{{ $slot }}</a>
</li>
