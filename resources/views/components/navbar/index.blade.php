@props([
    'label' => null,
    'menuLabel' => null,
    'sticky' => false,
    'container' => 'max-w-6xl',
    'variant' => 'default',
])

@php
    $label ??= __('giraffeui::ui.main');
    $menuLabel ??= __('giraffeui::ui.menu');
    $id = \JayAitch\GiraffeUI\Support\Id::make('gui-navbar');
    $anchor = \JayAitch\GiraffeUI\Support\Anchor::name($id);
@endphp

{{--
    A top bar: brand, links and actions. From the md breakpoint the links sit in the bar; below it, a menu button opens
    them in a native popover under the bar (Escape and clicking outside close it, and focus returns to the button).
    Actions stay in the bar at every size, so nothing in them (like a dropdown's id) is rendered twice.
--}}
<header
    style="anchor-name: {{ $anchor }}"
    {{ $attributes->class([
        'border-b border-zinc-200 dark:border-zinc-800',
        'bg-white dark:bg-zinc-950' => ! $sticky,
        'sticky top-0 z-30 bg-white/90 backdrop-blur dark:bg-zinc-950/90' => $sticky,
    ]) }}
>
    <div class="mx-auto flex h-16 items-center gap-6 px-4 sm:px-6 {{ $container }}">
        @isset($brand)
            <div class="flex shrink-0 items-center">{{ $brand }}</div>
        @endisset

        <nav aria-label="{{ $label }}" class="hidden md:block">
            {{-- pills: each link a rounded pill, the current one filled. --}}
            <ul class="group/navbar flex items-center gap-1" data-place="bar" data-variant="{{ $variant }}">{{ $slot }}</ul>
        </nav>

        <div class="ms-auto flex items-center gap-2">
            {{ $actions ?? '' }}

            <button
                type="button"
                commandfor="{{ $id }}-menu"
                command="toggle-popover"
                aria-label="{{ $menuLabel }}"
                class="flex size-9 cursor-pointer items-center justify-center rounded-gui text-zinc-600 transition-colors hover:bg-zinc-100 hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-gui-primary-text md:hidden dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100"
            >
                <x-gui::icon name="bars-3" />
            </button>
        </div>
    </div>

    <div
        id="{{ $id }}-menu"
        popover
        style="position-anchor: {{ $anchor }}"
        class="inset-auto m-0 w-[anchor-size(width)] border-b border-zinc-200 bg-white p-3 shadow-lg [position-area:bottom] md:hidden dark:border-zinc-800 dark:bg-zinc-950"
    >
        <nav aria-label="{{ $label }}">
            <ul class="group/navbar space-y-1" data-place="menu">{{ $slot }}</ul>
        </nav>
    </div>
</header>
