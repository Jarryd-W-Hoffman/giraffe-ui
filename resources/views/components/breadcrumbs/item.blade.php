@props([
    'href' => null,
])

{{-- An item without an href is the current page. --}}
<li class="group/crumb inline-flex items-center gap-1.5">
    <x-gui::icon name="chevron-right" size="sm" class="text-zinc-400 group-first/crumb:hidden dark:text-zinc-600" />

    @if ($href)
        <a href="{{ $href }}" {{ $attributes->class('inline-flex items-center gap-1.5 rounded-gui transition-colors hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text dark:hover:text-zinc-100') }}>{{ $slot }}</a>
    @else
        <span aria-current="page" {{ $attributes->class('inline-flex items-center gap-1.5 font-medium text-zinc-900 dark:text-zinc-100') }}>{{ $slot }}</span>
    @endif
</li>
