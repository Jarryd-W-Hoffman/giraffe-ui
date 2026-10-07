@props([
    'anchor',
])

{{--
    An item's name beside it, while the sidebar is a rail of icons: on hover, or on keyboard focus. It's fixed and
    anchored, so the sidebar's scrolling doesn't clip it. Hidden from screen readers, which read the item's own label.
--}}
<span
    aria-hidden="true"
    style="position-anchor: {{ $anchor }}"
    class="pointer-events-none fixed z-50 ms-2 hidden w-max max-w-xs rounded-gui bg-zinc-900 px-2 py-1 text-xs font-medium text-white shadow-sm [position-area:x-end] in-data-collapsed:group-hover/item:block in-data-collapsed:group-focus-visible/item:block dark:bg-zinc-100 dark:text-zinc-900"
>{{ $slot }}</span>
