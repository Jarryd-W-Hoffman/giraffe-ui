@props([
    'title' => null,
    'description' => null,
    'icon' => null,
    'variant' => 'default',
])

{{--
    A card. filled is borderless and a step off the page, with rounder corners (they grow with the theme's radius),
    for dashboards that group content by tone rather than by lines.
--}}
<div data-variant="{{ $variant }}" {{ $attributes->class([
    'group/card min-w-0 text-zinc-900 dark:text-zinc-100',
    'rounded-gui border border-zinc-200 bg-white shadow-xs dark:border-zinc-800 dark:bg-zinc-900' => $variant !== 'filled',
    'rounded-[calc(var(--radius-gui)+1.125rem)] bg-zinc-100 dark:bg-zinc-900' => $variant === 'filled',
]) }}>
    @if ($title || $description || isset($actions))
        <div class="flex items-start justify-between gap-4 px-6 pt-6 group-data-[variant=filled]/card:items-center">
            <div class="min-w-0 space-y-1">
                @if ($title)
                    <h3 class="flex items-center gap-2.5 leading-none font-semibold group-data-[variant=filled]/card:text-lg group-data-[variant=filled]/card:font-normal">
                        @if ($icon)
                            <x-gui::icon :name="$icon" class="text-zinc-500 dark:text-zinc-400" />
                        @endif
                        {{ $title }}
                    </h3>
                @endif
                @if ($description)
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $description }}</p>
                @endif
            </div>

            @isset($actions)
                <div class="flex shrink-0 items-center gap-2">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    @if (! \JayAitch\GiraffeUI\Support\Slot::isBlank($slot))
        <div class="p-6">{{ $slot }}</div>
    @endif

    @isset($footer)
        <div class="flex items-center gap-3 border-t border-zinc-200 px-6 py-4 group-data-[variant=filled]/card:border-zinc-200/80 dark:border-zinc-800">{{ $footer }}</div>
    @endisset
</div>
