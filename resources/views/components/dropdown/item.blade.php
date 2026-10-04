@aware(['guiDropdown' => null])

@props([
    'href' => null,
    'color' => null,
    'disabled' => false,
])

@php
    $classes = [
        'flex w-full cursor-pointer items-center gap-2 rounded-[calc(var(--radius-gui)-2px)] px-2.5 py-1.5 text-left whitespace-nowrap transition-colors',
        'focus:outline-none focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-gui-primary-text',
        'aria-disabled:pointer-events-none aria-disabled:opacity-50',
        $color === 'danger'
            ? 'text-gui-danger-text hover:bg-gui-danger/10 focus:bg-gui-danger/10'
            : 'text-zinc-700 hover:bg-zinc-100 hover:text-zinc-900 focus:bg-zinc-100 focus:text-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-zinc-100 dark:focus:bg-zinc-800 dark:focus:text-zinc-100',
    ];
@endphp

@if ($href && ! $disabled)
    <a href="{{ $href }}" role="menuitem" {{ $attributes->class($classes) }}>
        {{ $leading ?? '' }}{{ $slot }}
    </a>
@else
    {{--
        command="hide-popover" closes the menu after the click handler (e.g. wire:click) runs.
        It's a default, so an item can use its own command, like opening a modal (which also closes the menu).
    --}}
    <button {{ $attributes
        ->merge(['type' => 'button', 'role' => 'menuitem'] + ($disabled
            ? ['aria-disabled' => 'true']
            : ['commandfor' => $guiDropdown['id'] ?? null, 'command' => 'hide-popover']))
        ->except($disabled ? ['commandfor', 'command'] : [])
        ->class($classes) }}>
        {{ $leading ?? '' }}{{ $slot }}
    </button>
@endif
