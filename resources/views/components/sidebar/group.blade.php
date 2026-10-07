@props([
    'heading' => null,
    'collapsible' => false,
    'open' => true,
])

@php
    $collapsible = $collapsible && $heading;
    // A closed group still opens when it holds the current page.
    $open = $open || ($collapsible && str_contains($slot->toHtml(), 'aria-current="page"'));
    $anchor = $collapsible ? \JayAitch\GiraffeUI\Support\Anchor::name(\JayAitch\GiraffeUI\Support\Id::make('gui-sidebar-group')) : null;
@endphp

{{--
    A heading names the group without an id, so the same sidebar can render twice (column and drawer). collapsible
    makes the heading a native disclosure that shows and hides the group. In the rail, headings become dividers.
--}}
{{-- A collapsible group is named by its summary; a plain one by aria-label, since its heading is hidden from screen readers. --}}
<div @if ($heading && ! $collapsible) role="group" aria-label="{{ $heading }}" @endif {{ $attributes }}>
    @if ($collapsible)
        <details @if ($open) open @endif class="group/section">
            <summary
                style="anchor-name: {{ $anchor }}"
                x-data
                x-on:click="if ($el.closest('[data-collapsed]')) { $dispatch('gui-sidebar-expand'); if ($el.parentElement.open) $event.preventDefault() }"
                class="group/item mb-1 flex cursor-pointer list-none items-center justify-between gap-2 rounded-[calc(var(--radius-gui)-2px)] px-2 py-1 text-xs font-semibold tracking-wide text-zinc-500 uppercase hover:text-zinc-900 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-gui-primary-text in-data-collapsed:justify-center dark:text-zinc-400 dark:hover:text-zinc-100 [&::-webkit-details-marker]:hidden"
            >
                <span class="truncate in-data-collapsed:sr-only">{{ $heading }}</span>
                <x-gui::icon name="chevron-down" size="xs" class="shrink-0 transition-transform not-group-open/section:-rotate-90 in-data-collapsed:hidden motion-reduce:transition-none" />
                {{-- In the rail: a divider while open, and a "more" mark while closed. --}}
                <span class="hidden h-px w-6 bg-zinc-300 in-data-collapsed:group-open/section:block dark:bg-zinc-700" aria-hidden="true"></span>
                <x-gui::icon name="ellipsis-horizontal" size="sm" class="hidden in-data-collapsed:not-group-open/section:block" />
                <x-gui::sidebar.tooltip :anchor="$anchor">{{ $heading }}</x-gui::sidebar.tooltip>
            </summary>
            <ul class="space-y-0.5">
                {{ $slot }}
            </ul>
        </details>
    @else
        @if ($heading)
            <p class="mb-1 px-2 text-xs font-semibold tracking-wide text-zinc-500 uppercase in-data-collapsed:hidden dark:text-zinc-400" aria-hidden="true">{{ $heading }}</p>
            <span class="mx-auto mb-2 hidden h-px w-6 bg-zinc-300 in-data-collapsed:block dark:bg-zinc-700" aria-hidden="true"></span>
        @endif
        <ul class="space-y-0.5">
            {{ $slot }}
        </ul>
    @endif
</div>
