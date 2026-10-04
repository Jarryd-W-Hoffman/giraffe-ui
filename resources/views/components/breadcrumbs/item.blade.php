@props([
    'href' => null,
])

{{-- An item without an href is the current page. --}}
<li class="group/crumb inline-flex items-center gap-1.5">
    <svg class="size-4 shrink-0 text-zinc-400 group-first/crumb:hidden dark:text-zinc-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
        <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
    </svg>

    @if ($href)
        <a href="{{ $href }}" {{ $attributes->class('inline-flex items-center gap-1.5 rounded-gui transition-colors hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text dark:hover:text-zinc-100') }}>{{ $slot }}</a>
    @else
        <span aria-current="page" {{ $attributes->class('inline-flex items-center gap-1.5 font-medium text-zinc-900 dark:text-zinc-100') }}>{{ $slot }}</span>
    @endif
</li>
