@props([
    'for' => 'command-palette',
    'variant' => 'pill',
    'description' => null,
    'shortcut' => 'K',
])

{{--
    A button that opens a command palette: a search pill with the keyboard shortcut, or a larger panel with a line
    of description. for is the palette's id. The shortcut is shown for sighted users; the palette listens for it.
--}}
<button
    type="button"
    commandfor="{{ $for }}"
    command="show-modal"
    @if ($shortcut) aria-keyshortcuts="Meta+{{ $shortcut }} Control+{{ $shortcut }}" @endif
    {{ $attributes->class([
        'cursor-pointer focus-visible:outline-2 focus-visible:outline-gui-primary-text',
        'flex items-center gap-2 rounded-full bg-zinc-100 py-1.5 ps-3 pe-1.5 text-sm text-zinc-600 transition-colors hover:bg-zinc-200 hover:text-zinc-900 dark:bg-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100' => $variant !== 'panel',
        'group flex w-full items-center justify-between gap-4 rounded-[calc(var(--radius-gui)+1.125rem)] bg-zinc-900 p-6 text-start text-white transition hover:bg-zinc-800 focus-visible:outline-offset-2 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200' => $variant === 'panel',
    ]) }}
>
    @if ($variant === 'panel')
        <span>
            <span class="block text-lg">{{ $slot }}</span>
            @if ($description)
                <span class="block text-sm text-zinc-400 dark:text-zinc-600">{{ $description }}</span>
            @endif
        </span>
        <x-gui::icon name="magnifying-glass" class="transition-transform group-hover:scale-110 motion-reduce:transition-none" />
    @else
        <x-gui::icon name="magnifying-glass" size="sm" />
        {{ $slot }}
        @if ($shortcut)
            <span class="flex gap-0.5" aria-hidden="true"><x-gui::kbd>⌘</x-gui::kbd><x-gui::kbd>{{ $shortcut }}</x-gui::kbd></span>
        @endif
    @endif
</button>
