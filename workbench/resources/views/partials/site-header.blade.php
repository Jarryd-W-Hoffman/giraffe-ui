{{--
    The site's header, shared by the landing page and the docs: GiraffeUI's navbar with the logo, the main sections,
    ⌘K search, the theme toggle and Get started.
      $search   the id of the page's command palette
      $section  which link is current: docs, components or examples (none on the landing page)
      $bordered a line under the bar, for pages whose content scrolls beneath it
      $leading  anything before the logo, such as the docs' menu button
--}}
@php
    $section ??= null;
    $bordered ??= false;
@endphp

<x-gui::navbar sticky container="max-w-7xl" @class([
    'bg-white/80! dark:bg-black/80!',
    'border-transparent!' => ! $bordered,
    'border-zinc-200! dark:border-zinc-800!' => $bordered,
])>
    <x-slot:brand>
        {{ $leading ?? '' }}
        <a href="/catalog/home" class="flex items-center gap-2.5 rounded-full text-lg font-semibold tracking-tight focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-gui-primary-text">
            @include('catalog::partials.logo', ['class' => 'size-9'])
            GiraffeUI
        </a>
    </x-slot:brand>

    <x-gui::navbar.item href="/catalog/introduction" :current="$section === 'docs'">Docs</x-gui::navbar.item>
    <x-gui::navbar.item href="/catalog/button" :current="$section === 'components'">Components</x-gui::navbar.item>
    <x-gui::navbar.item href="/catalog/examples/dashboard" :current="$section === 'examples'">Examples</x-gui::navbar.item>
    <x-gui::navbar.item href="https://github.com/Jarryd-W-Hoffman/GiraffeUI" :current="false">GitHub</x-gui::navbar.item>

    <x-slot:actions>
        <x-gui::command-palette.trigger :for="$search" class="max-sm:hidden">Search components</x-gui::command-palette.trigger>
        <x-gui::theme-switcher variant="toggle" size="sm" />
        <x-gui::button href="/catalog/installation" color="ink" shape="pill" size="sm" class="max-sm:hidden">Get started</x-gui::button>
    </x-slot:actions>
</x-gui::navbar>
