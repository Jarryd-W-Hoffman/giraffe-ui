@props([
    'href' => null,
    'icon' => null,
    'keywords' => null,
    'shortcut' => null,
])

@php
    $id = \JayAitch\GiraffeUI\Support\Id::make('gui-cmd-item');
    // Search the visible text plus any extra keywords.
    $search = \Illuminate\Support\Str::lower(trim(strip_tags((string) $slot).' '.$keywords));
    // Close the palette on click, then run the item's own x-on:click / @click, if any.
    $userClick = $attributes->get('x-on:click') ?? $attributes->get('@click');
    $click = "\$el.closest('dialog')?.close()".($userClick ? "; {$userClick}" : '');
    $classes = 'flex w-full cursor-pointer items-center gap-3 rounded-[calc(var(--radius-gui)-2px)] px-2 py-2 text-left text-sm text-zinc-700 data-[active=true]:bg-zinc-100 data-[active=true]:text-zinc-900 dark:text-zinc-300 dark:data-[active=true]:bg-zinc-800 dark:data-[active=true]:text-zinc-100';
@endphp

{{--
    An option the palette activates with Enter or a click. It's a real link or button underneath, so
    href navigates and wire:click / x-on:click run as usual. tabindex="-1": focus stays in the search box.
--}}
<{{ $href ? 'a' : 'button' }}
    id="{{ $id }}"
    role="option"
    tabindex="-1"
    @if ($href) href="{{ $href }}" @else type="button" @endif
    data-search="{{ $search }}"
    x-show="matches($el)"
    x-bind:aria-selected="active === $el.id"
    x-bind:data-active="active === $el.id"
    x-on:mousemove="active = $el.id"
    x-on:click="{{ $click }}"
    {{ $attributes->except(['x-on:click', '@click'])->class($classes) }}
>
    @if ($icon)
        <x-gui::icon :name="$icon" class="text-zinc-500 dark:text-zinc-400" />
    @endif
    <span class="min-w-0 flex-1 truncate">{{ $slot }}</span>
    @if ($shortcut)
        <span class="ml-auto flex shrink-0 gap-1" aria-hidden="true">
            @foreach (explode(' ', $shortcut) as $key)
                <x-gui::kbd>{{ $key }}</x-gui::kbd>
            @endforeach
        </span>
    @endif
</{{ $href ? 'a' : 'button' }}>
