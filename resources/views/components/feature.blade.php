@props([
    'icon' => null,
    'title',
    'heading' => 'h3',
    'variant' => 'tile',
])

@php($variant = in_array($variant, ['tile', 'inline', 'plain'], true) ? $variant : 'tile')

{{--
    A feature for a landing page: an icon, a title and a sentence or two. The icon is decoration.
      tile    a filled panel, the icon on a tile above (the default)
      inline  no panel, the icon beside the text, for denser lists
      plain   no panel, the icon above, for an airy grid
--}}
@if ($variant === 'inline')
    <div {{ $attributes->class('flex gap-4') }}>
        @if ($icon)
            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-gui-primary/10 text-gui-primary-text">
                <x-gui::icon :name="$icon" size="sm" />
            </span>
        @endif
        <div>
            <{{ $heading }} class="font-medium">{{ $title }}</{{ $heading }}>
            <p class="mt-1 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ $slot }}</p>
        </div>
    </div>
@else
    <div {{ $attributes->class([
        'group rounded-3xl bg-zinc-100 p-7 transition-colors hover:bg-zinc-200/70 dark:bg-zinc-900 dark:hover:bg-zinc-800/80' => $variant === 'tile',
    ]) }}>
        @if ($icon)
            <span @class([
                'flex items-center justify-center text-gui-primary-text',
                'size-11 rounded-2xl bg-white shadow-xs transition-transform group-hover:-rotate-6 group-hover:scale-110 motion-reduce:transition-none dark:bg-black' => $variant === 'tile',
                'size-10 rounded-full ring-1 ring-zinc-200 dark:ring-zinc-800' => $variant === 'plain',
            ])>
                <x-gui::icon :name="$icon" />
            </span>
        @endif
        <{{ $heading }} @class(['text-lg', 'mt-6' => $icon && $variant === 'tile', 'mt-4' => $icon && $variant === 'plain'])>{{ $title }}</{{ $heading }}>
        <p class="mt-2 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ $slot }}</p>
    </div>
@endif
