@props([
    'search' => 'Search Acme',
])

{{--
    The app around the Dashboard, Projects, Inbox and Customers examples: a workspace with a collapsible sidebar
    (collapse it to a rail of icons with the button at the bottom; the choice is remembered in this browser), search
    and notifications in the top bar, on black (or white). Lime and violet replace the first and seventh chart colours.
--}}
@php
    $photo = \Workbench\App\Catalog::data()['photo'];
@endphp

<div class="[--color-gui-chart-1:var(--color-lime-600)] [--color-gui-chart-7:var(--color-violet-600)] [--gui-chart-surface:var(--color-zinc-100)] dark:[--color-gui-chart-1:var(--color-lime-300)] dark:[--color-gui-chart-7:var(--color-violet-500)] dark:[--gui-chart-surface:var(--color-zinc-900)]">
<x-gui::sidebar-layout collapsible class="dark:bg-black!">
    <x-slot:brand>
        <x-example::brand />
    </x-slot:brand>

    <x-slot:sidebar>
        <x-gui::sidebar>
            <x-gui::sidebar.group>
                <x-gui::sidebar.item href="/catalog/examples/dashboard" icon="home">Overview</x-gui::sidebar.item>
                <x-gui::sidebar.submenu label="Projects" icon="squares-2x2" badge="4">
                    <x-gui::sidebar.item href="/catalog/examples/projects">Website relaunch</x-gui::sidebar.item>
                    <x-gui::sidebar.item href="#">Mobile app</x-gui::sidebar.item>
                    <x-gui::sidebar.item href="#">Spring campaign</x-gui::sidebar.item>
                    <x-gui::sidebar.item href="#">Billing rewrite</x-gui::sidebar.item>
                </x-gui::sidebar.submenu>
                @if (\Workbench\App\Catalog::livewire())
                    {{-- The Customers page is a Livewire component, so it only exists when Livewire is installed. --}}
                    <x-gui::sidebar.item href="/catalog/examples/customers" icon="users">Customers</x-gui::sidebar.item>
                @endif
                <x-gui::sidebar.item href="/catalog/examples/inbox" icon="inbox" badge="3">Inbox</x-gui::sidebar.item>
                <x-gui::sidebar.item href="#" icon="calendar">Calendar</x-gui::sidebar.item>
                {{-- No href: a button that opens the settings modal. --}}
                <x-gui::sidebar.item icon="cog-6-tooth" commandfor="settings" command="show-modal">Settings</x-gui::sidebar.item>
            </x-gui::sidebar.group>
            <x-gui::sidebar.group heading="Teams" collapsible>
                <x-gui::sidebar.item href="#" icon="paint-brush">Design</x-gui::sidebar.item>
                <x-gui::sidebar.item href="#" icon="code-bracket">Engineering</x-gui::sidebar.item>
                <x-gui::sidebar.item href="#" icon="megaphone">Marketing</x-gui::sidebar.item>
            </x-gui::sidebar.group>
        </x-gui::sidebar>
    </x-slot:sidebar>

    <x-slot:footer>
        <div class="flex items-center gap-3 px-2 py-1">
            <x-gui::avatar :src="$photo" name="Ada Lovelace" size="sm" />
            <div class="min-w-0 text-sm">
                <p class="truncate font-medium">Ada Lovelace</p>
                <p class="truncate text-zinc-600 dark:text-zinc-400">ada@example.com</p>
            </div>
        </div>
    </x-slot:footer>

    <x-slot:header>
        <x-gui::input type="search" :placeholder="$search" :aria-label="$search" class="max-w-64 rounded-full!" size="sm">
            <x-slot:leading>
                <x-gui::icon name="magnifying-glass" size="sm" />
            </x-slot:leading>
        </x-gui::input>
        <x-gui::button variant="ghost" color="ink" shape="pill" icon size="sm" aria-label="Notifications"><x-gui::icon name="bell" size="sm" /></x-gui::button>
        <x-gui::dropdown id="account-menu" position="bottom-end">
            <x-slot:trigger>
                <button type="button" commandfor="account-menu" command="toggle-popover" aria-haspopup="menu" aria-label="Account" class="cursor-pointer rounded-full focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text">
                    <x-gui::avatar :src="$photo" name="Ada Lovelace" size="sm" />
                </button>
            </x-slot:trigger>
            <x-gui::dropdown.item commandfor="settings" command="show-modal">Settings</x-gui::dropdown.item>
            <x-gui::dropdown.separator />
            <x-gui::dropdown.item href="/catalog/examples/sign-in">Sign out</x-gui::dropdown.item>
        </x-gui::dropdown>
    </x-slot:header>

    {{ $slot }}
</x-gui::sidebar-layout>
</div>

<x-example::settings-modal />
