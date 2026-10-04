@props([
    'title' => null,
    'description' => null,
])

<div {{ $attributes->class('rounded-gui border border-zinc-200 bg-white text-zinc-900 shadow-xs dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-100') }}>
    @if ($title || $description || isset($actions))
        <div class="flex items-start justify-between gap-4 px-6 pt-6">
            <div class="min-w-0 space-y-1">
                @if ($title)
                    <h3 class="leading-none font-semibold">{{ $title }}</h3>
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
        <div class="flex items-center gap-3 border-t border-zinc-200 px-6 py-4 dark:border-zinc-800">{{ $footer }}</div>
    @endisset
</div>
