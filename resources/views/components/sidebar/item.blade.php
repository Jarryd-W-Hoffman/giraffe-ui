@props([
    'href',
    'icon' => null,
    'current' => null,
    'badge' => null,
])

@php
    // Without a current prop, an item is current when its URL is the page being shown.
    $current ??= url($href) === url()->current();
@endphp

<li>
    <a
        href="{{ $href }}"
        @if ($current) aria-current="page" @endif
        {{ $attributes->class([
            'group/item flex items-center gap-2.5 rounded-[calc(var(--radius-gui)-2px)] px-2 py-1.5 text-sm font-medium text-zinc-700 transition-colors',
            'hover:bg-zinc-200/60 hover:text-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-zinc-100',
            'aria-[current=page]:bg-zinc-200/70 aria-[current=page]:text-zinc-900 dark:aria-[current=page]:bg-zinc-800 dark:aria-[current=page]:text-zinc-100',
            'focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-gui-primary-text',
        ]) }}
    >
        @if ($icon)
            <x-gui::icon :name="$icon" class="text-zinc-500 group-hover/item:text-zinc-700 group-aria-[current=page]/item:text-gui-primary-text dark:text-zinc-400 dark:group-hover/item:text-zinc-200" />
        @endif
        <span class="min-w-0 flex-1 truncate">{{ $slot }}</span>
        @if ($badge !== null)
            <x-gui::badge size="sm">{{ $badge }}</x-gui::badge>
        @endif
    </a>
</li>
