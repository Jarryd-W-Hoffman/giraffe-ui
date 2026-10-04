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
        <x-gui::icon name="chevron-down" size="sm" class="text-zinc-500 transition-transform group-open:rotate-180 motion-reduce:transition-none dark:text-zinc-400" />
    </summary>
    <div class="pb-4 text-sm text-zinc-600 dark:text-zinc-400">{{ $slot }}</div>
</details>
