@props([
    'drawerId' => 'gui-sidebar-drawer',
    'drawerTitle' => null,
    'menuLabel' => null,
])

@php
    $drawerTitle ??= __('giraffeui::ui.navigation');
    $menuLabel ??= __('giraffeui::ui.open_navigation');
@endphp

{{--
    An application shell. From the lg breakpoint the sidebar is a sticky column; below it, a menu button opens the
    same navigation in a Drawer (a native modal dialog, so focus is trapped and Escape closes it).
--}}
<div {{ $attributes->class('min-h-dvh bg-white text-zinc-900 lg:grid lg:grid-cols-[16rem_minmax(0,1fr)] dark:bg-zinc-950 dark:text-zinc-100') }}>
    <aside class="hidden border-r border-zinc-200 bg-zinc-50 lg:sticky lg:top-0 lg:flex lg:h-dvh lg:flex-col dark:border-zinc-800 dark:bg-zinc-900">
        @isset($brand)
            <div class="flex h-14 shrink-0 items-center px-5">{{ $brand }}</div>
        @endisset
        <div class="flex-1 overflow-y-auto px-3 py-4">{{ $sidebar }}</div>
        @isset($footer)
            <div class="shrink-0 border-t border-zinc-200 p-3 dark:border-zinc-800">{{ $footer }}</div>
        @endisset
    </aside>

    <div class="flex min-w-0 flex-col">
        <header @class([
            'sticky top-0 z-20 flex h-14 shrink-0 items-center gap-3 border-b border-zinc-200 bg-white/90 px-4 backdrop-blur sm:px-6 lg:px-8 dark:border-zinc-800 dark:bg-zinc-950/90',
            'lg:hidden' => ! isset($header),
        ])>
            <x-gui::button variant="ghost" color="secondary" size="sm" class="-ml-1.5 px-1.5! lg:hidden" :commandfor="$drawerId" command="show-modal" :aria-label="$menuLabel">
                <x-gui::icon name="bars-3" />
            </x-gui::button>
            @isset($brand)
                <div class="lg:hidden">{{ $brand }}</div>
            @endisset
            @isset($header)
                <div class="flex min-w-0 flex-1 items-center justify-end gap-2">{{ $header }}</div>
            @endisset
        </header>

        <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">{{ $slot }}</main>
    </div>
</div>

<x-gui::drawer :id="$drawerId" :title="$drawerTitle" side="left" size="sm">
    {{ $sidebar }}
    @isset($footer)
        <div class="mt-6 border-t border-zinc-200 pt-4 dark:border-zinc-800">{{ $footer }}</div>
    @endisset
</x-gui::drawer>
