@props([
    'drawerId' => 'gui-sidebar-drawer',
    'drawerTitle' => null,
    'menuLabel' => null,
    'collapsible' => false,
    'collapsed' => false,
    'persist' => 'gui-sidebar-collapsed',
    'collapseLabel' => null,
    'expandLabel' => null,
])

@php
    $drawerTitle ??= __('giraffeui::ui.navigation');
    $menuLabel ??= __('giraffeui::ui.open_navigation');
    $collapseLabel ??= __('giraffeui::ui.collapse_sidebar');
    $expandLabel ??= __('giraffeui::ui.expand_sidebar');
@endphp

{{--
    An application shell. From the lg breakpoint the sidebar is a sticky column; below it, a menu button opens the
    same navigation in a Drawer (a native modal dialog, so focus is trapped and Escape closes it).

    collapsible adds a button that shrinks the column to a rail of icons. Labels stay in the page for screen readers,
    and show as tooltips on hover and focus. The choice is kept in this browser (persist names the localStorage key;
    null keeps nothing) and applied before the page paints. The drawer always shows the full sidebar.
--}}
<div
    data-sidebar-layout
    @if ($collapsible && $collapsed) data-collapsed @endif
    {{ $attributes->class([
        'min-h-dvh bg-white text-zinc-900 lg:grid lg:grid-cols-[16rem_minmax(0,1fr)] dark:bg-zinc-950 dark:text-zinc-100',
        'lg:transition-[grid-template-columns] lg:duration-200 lg:data-collapsed:grid-cols-[4rem_minmax(0,1fr)] motion-reduce:transition-none' => $collapsible,
    ]) }}
>
    @if ($collapsible && $persist)
        {{-- Before anything paints, so a collapsed sidebar never flashes open. --}}
        <script>
            (function (layout) {
                try {
                    const saved = localStorage.getItem(@js($persist));
                    if (saved !== null) layout.toggleAttribute('data-collapsed', saved === '1');
                } catch (e) {}
            })(document.currentScript.parentElement);
        </script>
    @endif

    <aside
        @if ($collapsible)
            x-data="{
                layout: null,
                collapsed: false,
                init() {
                    this.layout = this.$el.closest('[data-sidebar-layout]');
                    this.collapsed = this.layout.hasAttribute('data-collapsed');
                },
                set(collapsed) {
                    this.collapsed = collapsed;
                    this.layout.toggleAttribute('data-collapsed', collapsed);
                    @if ($persist) try { localStorage.setItem(@js($persist), collapsed ? '1' : '0'); } catch (e) {} @endif
                },
            }"
            {{-- Opening a submenu or group from the rail expands the sidebar first. --}}
            x-on:gui-sidebar-expand="set(false)"
        @endif
        class="hidden border-e border-zinc-200 bg-zinc-50 lg:sticky lg:top-0 lg:flex lg:h-dvh lg:flex-col dark:border-zinc-800 dark:bg-zinc-900"
    >
        @isset($brand)
            {{-- In the rail only the start of the brand shows, so lead it with a logo. --}}
            <div class="flex h-14 shrink-0 items-center overflow-hidden px-5 in-data-collapsed:px-[1.125rem]">
                <div class="min-w-0 in-data-collapsed:w-7 in-data-collapsed:overflow-hidden"><div class="w-max">{{ $brand }}</div></div>
            </div>
        @endisset
        <div class="flex-1 overflow-x-hidden overflow-y-auto px-3 py-4">{{ $sidebar }}</div>
        @if ($collapsible)
            <div class="shrink-0 px-3 pb-3">
                @php($toggleAnchor = \JayAitch\GiraffeUI\Support\Anchor::name(\JayAitch\GiraffeUI\Support\Id::make('gui-sidebar-toggle')))
                <button
                    type="button"
                    x-cloak
                    style="anchor-name: {{ $toggleAnchor }}"
                    x-on:click="set(! collapsed)"
                    class="group/item relative flex w-full cursor-pointer items-center gap-2.5 rounded-[calc(var(--radius-gui)-2px)] px-2 py-1.5 text-sm font-medium text-zinc-600 transition-colors hover:bg-zinc-200/60 hover:text-zinc-900 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-gui-primary-text in-data-collapsed:justify-center dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100"
                >
                    <x-gui::icon name="chevron-double-left" class="transition-transform in-data-collapsed:rotate-180 motion-reduce:transition-none" />
                    <span class="truncate in-data-collapsed:hidden">{{ $collapseLabel }}</span>
                    <span class="hidden in-data-collapsed:sr-only">{{ $expandLabel }}</span>
                    <x-gui::sidebar.tooltip :anchor="$toggleAnchor">{{ $expandLabel }}</x-gui::sidebar.tooltip>
                </button>
            </div>
        @endif
        @isset($footer)
            {{-- Like the brand, only the start of the footer shows in the rail: lead it with an avatar. --}}
            <div class="shrink-0 overflow-hidden border-t border-zinc-200 p-3 dark:border-zinc-800">{{ $footer }}</div>
        @endisset
    </aside>

    <div class="flex min-w-0 flex-col">
        <header @class([
            'sticky top-0 z-20 flex h-14 shrink-0 items-center gap-3 border-b border-zinc-200 bg-white/90 px-4 backdrop-blur sm:px-6 lg:px-8 dark:border-zinc-800 dark:bg-zinc-950/90',
            'lg:hidden' => ! isset($header),
        ])>
            <x-gui::button variant="ghost" color="secondary" size="sm" class="-ms-1.5 px-1.5! lg:hidden" :commandfor="$drawerId" command="show-modal" :aria-label="$menuLabel">
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
