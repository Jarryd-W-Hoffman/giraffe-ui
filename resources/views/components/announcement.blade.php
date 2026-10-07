@props([
    'href',
    'badge' => 'New',
    'variant' => 'pill',
])

{{--
    A small pill that links to something new: a badge, a sentence and an arrow. For the top of a landing page or a
    changelog. The badge is part of the link's text, so screen readers hear "New: Charts…".
--}}
{{-- pill: a ringed pill on a frosted background (the default). subtle: no pill, just the badge and the words. --}}
<a href="{{ $href }}" {{ $attributes->class([
    'group inline-flex max-w-full items-center gap-2 text-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text',
    'rounded-full bg-white/70 py-1 ps-1 pe-3 ring-1 ring-zinc-200 backdrop-blur transition hover:ring-zinc-300 dark:bg-zinc-900/70 dark:ring-zinc-800 dark:hover:ring-zinc-700' => $variant !== 'subtle',
    'rounded-sm text-zinc-700 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-zinc-100' => $variant === 'subtle',
]) }}>
    @if ($badge)
        <x-gui::badge shape="pill" size="sm" variant="solid" class="bg-lime-300! text-zinc-900!">{{ $badge }}<span class="sr-only">:</span></x-gui::badge>
    @endif
    <span class="truncate">{{ $slot }}</span>
    <x-gui::icon name="arrow-right" size="xs" class="shrink-0 transition-transform group-hover:translate-x-0.5 motion-reduce:transition-none" />
</a>
