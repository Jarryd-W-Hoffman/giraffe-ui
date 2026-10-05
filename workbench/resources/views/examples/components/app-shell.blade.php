{{-- The app around the Dashboard and Settings examples: brand, navigation, signed-in user and top bar. --}}
@php
    $photo = \Workbench\App\Catalog::data()['photo'];
@endphp

<x-gui::sidebar-layout>
    <x-slot:brand>
        <a href="/catalog/examples/dashboard" class="flex items-center gap-2 font-semibold">
            <span class="flex size-7 items-center justify-center rounded-gui bg-gui-primary text-sm text-gui-primary-foreground" aria-hidden="true">A</span>
            Acme
        </a>
    </x-slot:brand>

    <x-slot:sidebar>
        <x-gui::sidebar>
            <x-gui::sidebar.group>
                <x-gui::sidebar.item href="/catalog/examples/dashboard" icon="home">Dashboard</x-gui::sidebar.item>
                <x-gui::sidebar.item href="#" icon="folder" badge="3">Projects</x-gui::sidebar.item>
                @if (\Workbench\App\Catalog::livewire())
                    {{-- The Customers page is a Livewire component, so it only exists when Livewire is installed. --}}
                    <x-gui::sidebar.item href="/catalog/examples/customers" icon="user">Customers</x-gui::sidebar.item>
                @endif
                <x-gui::sidebar.item href="#" icon="calendar">Calendar</x-gui::sidebar.item>
                <x-gui::sidebar.item href="/catalog/examples/settings" icon="cog-6-tooth">Settings</x-gui::sidebar.item>
            </x-gui::sidebar.group>
            <x-gui::sidebar.group heading="Help">
                <x-gui::sidebar.item href="/catalog/introduction" icon="information-circle">Documentation</x-gui::sidebar.item>
                <x-gui::sidebar.item href="#" icon="magnifying-glass">Search</x-gui::sidebar.item>
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
        <x-gui::input type="search" placeholder="Search" aria-label="Search" class="max-w-64" size="sm">
            <x-slot:leading>
                <x-gui::icon name="magnifying-glass" size="sm" />
            </x-slot:leading>
        </x-gui::input>

        <x-gui::dropdown id="account-menu" position="bottom-end">
            <x-slot:trigger>
                <button type="button" commandfor="account-menu" command="toggle-popover" aria-haspopup="menu" aria-label="Account" class="cursor-pointer rounded-full focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text">
                    <x-gui::avatar :src="$photo" name="Ada Lovelace" size="sm" />
                </button>
            </x-slot:trigger>
            <x-gui::dropdown.item href="#">Profile</x-gui::dropdown.item>
            <x-gui::dropdown.item href="/catalog/examples/settings">Settings</x-gui::dropdown.item>
            <x-gui::dropdown.separator />
            <x-gui::dropdown.item href="/catalog/examples/sign-in">Sign out</x-gui::dropdown.item>
        </x-gui::dropdown>
    </x-slot:header>

    {{ $slot }}
</x-gui::sidebar-layout>
