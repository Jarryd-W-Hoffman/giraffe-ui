@props([
    'title',
    'description' => null,
])

<div {{ $attributes->class('flex flex-col items-center px-6 py-12 text-center') }}>
    @isset($icon)
        <div class="mb-4 flex size-12 items-center justify-center rounded-full bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400" aria-hidden="true">{{ $icon }}</div>
    @endisset

    <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ $title }}</h3>

    @if ($description)
        <p class="mt-1 max-w-sm text-sm text-zinc-600 dark:text-zinc-400">{{ $description }}</p>
    @endif

    @isset($actions)
        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">{{ $actions }}</div>
    @endisset
</div>
