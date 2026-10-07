@props([
    'href' => null,
    'icon' => null,
    'current' => null,
    'badge' => null,
])

@php
    // Without a current prop, an item is current when its URL is the page being shown.
    // Without an href it's a button, for an action such as opening a modal (commandfor, x-on:click).
    $current ??= $href !== null && url($href) === url()->current();
    $tag = $href !== null ? 'a' : 'button';
    // In the rail an item without an icon shows its first letter instead.
    $initial = mb_strtoupper(mb_substr(trim(strip_tags($slot->toHtml())), 0, 1));
    $anchor = \JayAitch\GiraffeUI\Support\Anchor::name(\JayAitch\GiraffeUI\Support\Id::make('gui-sidebar-item'));
@endphp

<li>
    <{{ $tag }}
        @if ($href !== null) href="{{ $href }}" @else type="button" @endif
        @if ($current) aria-current="page" @endif
        style="anchor-name: {{ $anchor }}"
        {{ $attributes->class([
            'group/item flex w-full cursor-pointer items-center gap-2.5 text-start rounded-[calc(var(--radius-gui)-2px)] px-2 py-1.5 text-sm font-medium text-zinc-700 transition-colors',
            'hover:bg-zinc-200/60 hover:text-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-zinc-100',
            'aria-[current=page]:bg-zinc-200/70 aria-[current=page]:text-zinc-900 dark:aria-[current=page]:bg-zinc-800 dark:aria-[current=page]:text-zinc-100',
            'focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-gui-primary-text',
            // In the rail: just the icon, centred. The label stays for screen readers.
            'in-data-collapsed:justify-center',
        ]) }}
    >
        @if ($icon)
            <x-gui::icon :name="$icon" class="text-zinc-500 group-hover/item:text-zinc-700 group-aria-[current=page]/item:text-gui-primary-text dark:text-zinc-400 dark:group-hover/item:text-zinc-200" />
        @else
            <span class="hidden size-5 items-center justify-center text-xs font-semibold text-zinc-500 in-data-collapsed:flex group-aria-[current=page]/item:text-gui-primary-text dark:text-zinc-400" aria-hidden="true">{{ $initial }}</span>
        @endif
        <span class="min-w-0 flex-1 truncate in-data-collapsed:sr-only">{{ $slot }}</span>
        @if ($badge !== null)
            <x-gui::badge size="sm" class="in-data-collapsed:sr-only">{{ $badge }}</x-gui::badge>
        @endif
        <x-gui::sidebar.tooltip :anchor="$anchor">{{ $slot }}@if ($badge !== null) ({{ $badge }})@endif</x-gui::sidebar.tooltip>
    </{{ $tag }}>
</li>
