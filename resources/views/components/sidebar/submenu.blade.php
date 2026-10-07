@props([
    'label',
    'icon' => null,
    'open' => null,
    'badge' => null,
])

@php
    // Open when it holds the current page, unless open says otherwise.
    $active = str_contains($slot->toHtml(), 'aria-current="page"');
    $open ??= $active;
    $initial = mb_strtoupper(mb_substr(trim(strip_tags($label)), 0, 1));
    $anchor = \JayAitch\GiraffeUI\Support\Anchor::name(\JayAitch\GiraffeUI\Support\Id::make('gui-sidebar-submenu'));
@endphp

{{--
    An item with items under it, as a native disclosure (<details>): it works without JavaScript, and Enter or Space
    open and close it. Its items are sidebar.item, indented under a guide line. In the rail its items are hidden;
    clicking it expands the sidebar and opens it.
--}}
<li>
    <details @if ($open) open @endif class="group/submenu">
        <summary
            style="anchor-name: {{ $anchor }}"
            x-data
            x-on:click="if ($el.closest('[data-collapsed]')) { $dispatch('gui-sidebar-expand'); if ($el.parentElement.open) $event.preventDefault() }"
            @class([
                'group/item flex cursor-pointer list-none items-center gap-2.5 rounded-[calc(var(--radius-gui)-2px)] px-2 py-1.5 text-sm font-medium transition-colors [&::-webkit-details-marker]:hidden',
                'hover:bg-zinc-200/60 hover:text-zinc-900 dark:hover:bg-zinc-800 dark:hover:text-zinc-100',
                'focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-gui-primary-text',
                'in-data-collapsed:justify-center',
                // The section holding the current page reads as active, more strongly in the rail where its items are hidden.
                $active ? 'text-zinc-900 in-data-collapsed:bg-zinc-200/70 dark:text-zinc-100 dark:in-data-collapsed:bg-zinc-800' : 'text-zinc-700 dark:text-zinc-300',
            ])
        >
            @if ($icon)
                <x-gui::icon :name="$icon" @class([
                    'group-hover/item:text-zinc-700 dark:group-hover/item:text-zinc-200',
                    $active ? 'text-gui-primary-text' : 'text-zinc-500 dark:text-zinc-400',
                ]) />
            @else
                <span class="hidden size-5 items-center justify-center text-xs font-semibold text-zinc-500 in-data-collapsed:flex dark:text-zinc-400" aria-hidden="true">{{ $initial }}</span>
            @endif
            <span class="min-w-0 flex-1 truncate in-data-collapsed:sr-only">{{ $label }}</span>
            @if ($badge !== null)
                <x-gui::badge size="sm" class="in-data-collapsed:sr-only">{{ $badge }}</x-gui::badge>
            @endif
            <x-gui::icon name="chevron-right" size="sm" class="shrink-0 text-zinc-500 transition-transform group-open/submenu:rotate-90! in-data-collapsed:hidden motion-reduce:transition-none dark:text-zinc-400" />
            <x-gui::sidebar.tooltip :anchor="$anchor">{{ $label }}</x-gui::sidebar.tooltip>
        </summary>
        <ul class="ms-[1.125rem] mt-0.5 space-y-0.5 border-s border-zinc-200 ps-2 in-data-collapsed:hidden dark:border-zinc-800">
            {{ $slot }}
        </ul>
    </details>
</li>
