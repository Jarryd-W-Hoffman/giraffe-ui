@aware(['guiAccordion' => null])

@props([
    'title',
    'open' => false,
])

{{-- wire:ignore.self keeps the open state when Livewire re-renders. --}}
<details
    @if ($guiAccordion['name'] ?? null) name="{{ $guiAccordion['name'] }}" @endif
    @if ($open) open @endif
    wire:ignore.self
    {{ $attributes->class('group') }}
>
    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 py-4 text-left text-sm font-medium text-zinc-900 hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text dark:text-zinc-100 [&::-webkit-details-marker]:hidden">
        {{ $title }}
        <svg class="size-4 shrink-0 text-zinc-500 transition-transform group-open:rotate-180 motion-reduce:transition-none dark:text-zinc-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
        </svg>
    </summary>
    <div class="pb-4 text-sm text-zinc-600 dark:text-zinc-400">{{ $slot }}</div>
</details>
