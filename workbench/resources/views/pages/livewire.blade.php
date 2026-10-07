@extends('catalog::layout')

@section('toc')
    <li><a href="#forms" class="hover:text-zinc-900 dark:hover:text-zinc-100">Forms and loading</a></li>
    <li><a href="#search" class="hover:text-zinc-900 dark:hover:text-zinc-100">Searching on the server</a></li>
    <li><a href="#uploads" class="hover:text-zinc-900 dark:hover:text-zinc-100">Uploads</a></li>
    <li><a href="#tables" class="hover:text-zinc-900 dark:hover:text-zinc-100">Tables</a></li>
    <li><a href="#tabs" class="hover:text-zinc-900 dark:hover:text-zinc-100">Tabs</a></li>
    <li><a href="#wizards" class="hover:text-zinc-900 dark:hover:text-zinc-100">Wizards</a></li>
    <li><a href="#overlays" class="hover:text-zinc-900 dark:hover:text-zinc-100">Modals and menus</a></li>
@endsection

@section('content')
    @php
        $form = <<<'BLADE'
            <form wire:submit="save">
                <x-gui::field label="Name" name="name" required>
                    <x-gui::input wire:model="name" />
                </x-gui::field>

                <x-gui::button type="submit">Save profile</x-gui::button>
            </form>
            BLADE;

        $save = <<<'PHP'
            public function save()
            {
                $this->validate();

                // ...

                $this->dispatch('gui-toast', message: 'Saved', color: 'success');
            }
            PHP;

        $search = <<<'BLADE'
            <x-gui::combobox wire:model="timezone" url="/timezones" :options="$popularTimezones" />
            BLADE;

        $table = <<<'BLADE'
            <x-gui::input type="search" wire:model.live.debounce.250ms="search" aria-label="Search" />

            <x-gui::table :sort="$sortBy" :direction="$sortDirection">
                <x-slot:head>
                    <x-gui::table.heading sortable="name" wire:click="sort('name')">Name</x-gui::table.heading>
                </x-slot:head>

                @foreach ($users as $user)
                    <x-gui::table.row wire:key="user-{{ $user->id }}">
                        <x-gui::table.cell>{{ $user->name }}</x-gui::table.cell>
                    </x-gui::table.row>
                @endforeach

                <x-slot:footer>
                    <x-gui::pagination :paginator="$users" livewire />
                </x-slot:footer>
            </x-gui::table>
            BLADE;

        $tabs = <<<'BLADE'
            <x-gui::tabs wire:model.live="tab" :selected="$tab" :tabs="['account' => 'Account', 'billing' => 'Billing']">
                …
            </x-gui::tabs>
            BLADE;

        $wizard = <<<'BLADE'
            <x-gui::stepper aria-label="Sign up">
                @foreach ($steps as $key => $label)
                    @if ($loop->index < $current)
                        <x-gui::stepper.step status="complete" wire:click="goTo('{{ $key }}')">{{ $label }}</x-gui::stepper.step>
                    @else
                        <x-gui::stepper.step :status="$loop->index === $current ? 'current' : 'upcoming'">{{ $label }}</x-gui::stepper.step>
                    @endif
                @endforeach
            </x-gui::stepper>
            BLADE;

        $overlays = <<<'PHP'
            // Open a modal after an action
            $this->js("document.getElementById('confirm').showModal()");
            PHP;
    @endphp

    <h1 class="text-3xl font-bold tracking-tight">Livewire</h1>
    <p class="mt-3 text-lg text-zinc-600 dark:text-zinc-400">
        GiraffeUI doesn't need Livewire, but it's built to work well with it. The demos on this page are real Livewire 4 components running in the catalog.
    </p>

    <h2 id="forms" class="mt-12 scroll-mt-20 text-xl font-semibold tracking-tight">Forms and loading</h2>
    <p class="mt-2 text-zinc-600 dark:text-zinc-400">
        Put <code class="font-mono text-sm">wire:model</code> on the control and <code class="font-mono text-sm">name</code> on the field. Validation errors appear under the field and mark the control <code class="font-mono text-sm">aria-invalid</code>. Buttons show a spinner while their request runs, using the <code class="font-mono text-sm">data-loading</code> attribute Livewire adds. Try a two-letter name to see a server-side error; the browser checks required and email fields itself first.
    </p>
    <div class="mt-4 rounded-xl border border-zinc-200 p-6 sm:p-8 dark:border-zinc-800">
        <livewire:profile-form />
    </div>
    <div class="mt-4 space-y-3">
        @include('catalog::partials.code', ['code' => $form])
        @include('catalog::partials.code', ['code' => $save])
    </div>

    <h2 id="search" class="mt-12 scroll-mt-20 text-xl font-semibold tracking-tight">Searching on the server</h2>
    <p class="mt-2 text-zinc-600 dark:text-zinc-400">
        A combobox with <code class="font-mono text-sm">url</code> searches through an ordinary route that returns JSON, so it works the same with or without Livewire. In a Livewire form, <code class="font-mono text-sm">wire:model</code> binds the chosen value as usual. This demo searches PHP's 400+ timezones; see the <a href="/catalog/combobox" class="font-medium text-gui-primary-text hover:underline">combobox page</a> for the route.
    </p>
    <div class="mt-4 rounded-xl border border-zinc-200 p-6 sm:p-8 dark:border-zinc-800">
        <livewire:timezone-picker />
    </div>
    <div class="mt-4">
        @include('catalog::partials.code', ['code' => $search])
    </div>

    <h2 id="uploads" class="mt-12 scroll-mt-20 text-xl font-semibold tracking-tight">Uploads</h2>
    <p class="mt-2 text-zinc-600 dark:text-zinc-400">
        Put <code class="font-mono text-sm">wire:model</code> on a file upload in a component using <code class="font-mono text-sm">WithFileUploads</code>. It shows a progress bar while Livewire uploads, and validation errors appear under the field.
    </p>
    <div class="mt-4 rounded-xl border border-zinc-200 p-6 sm:p-8 dark:border-zinc-800">
        <livewire:avatar-upload />
    </div>

    <h2 id="tables" class="mt-12 scroll-mt-20 text-xl font-semibold tracking-tight">Tables</h2>
    <p class="mt-2 text-zinc-600 dark:text-zinc-400">
        On a sortable heading, <code class="font-mono text-sm">wire:click</code> goes on the sort button. Pagination with <code class="font-mono text-sm">livewire</code> calls <code class="font-mono text-sm">gotoPage</code>, <code class="font-mono text-sm">previousPage</code> and <code class="font-mono text-sm">nextPage</code>.
    </p>
    <div class="mt-4 rounded-xl border border-zinc-200 p-4 sm:p-8 dark:border-zinc-800">
        <livewire:users-table />
    </div>
    <div class="mt-4">
        @include('catalog::partials.code', ['code' => $table])
    </div>

    <h2 id="tabs" class="mt-12 scroll-mt-20 text-xl font-semibold tracking-tight">Tabs</h2>
    <p class="mt-2 text-zinc-600 dark:text-zinc-400">
        <code class="font-mono text-sm">wire:model</code> keeps the selected tab in sync both ways.
    </p>
    <div class="mt-4 rounded-xl border border-zinc-200 p-6 sm:p-8 dark:border-zinc-800">
        <livewire:settings-tabs />
    </div>
    <div class="mt-4">
        @include('catalog::partials.code', ['code' => $tabs])
    </div>

    <h2 id="wizards" class="mt-12 scroll-mt-20 text-xl font-semibold tracking-tight">Wizards</h2>
    <p class="mt-2 text-zinc-600 dark:text-zinc-400">
        A stepper shows where people are in a multi-step form. Keep the current step in a property, validate only that step's fields on Continue, and give completed steps a <code class="font-mono text-sm">wire:click</code> so they become buttons for going back. Moving between steps puts focus on the new step's heading. Try continuing with empty fields.
    </p>
    <div class="mt-4 rounded-xl border border-zinc-200 p-6 sm:p-8 dark:border-zinc-800">
        <livewire:signup-wizard />
    </div>
    <div class="mt-4">
        @include('catalog::partials.code', ['code' => $wizard])
    </div>

    <h2 id="overlays" class="mt-12 scroll-mt-20 text-xl font-semibold tracking-tight">Modals and menus</h2>
    <p class="mt-2 text-zinc-600 dark:text-zinc-400">
        Modals and accordions have <code class="font-mono text-sm">wire:ignore.self</code>, so a re-render doesn't close them. Dropdown items run their <code class="font-mono text-sm">wire:click</code> and then close the menu. Toasts listen for Livewire's <code class="font-mono text-sm">$this->dispatch('gui-toast', …)</code>.
    </p>
    <div class="mt-4">
        @include('catalog::partials.code', ['code' => $overlays])
    </div>
@endsection
