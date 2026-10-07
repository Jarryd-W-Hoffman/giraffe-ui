@extends('catalog::examples.layout')

@section('content')
    <x-example::app search="Search customers">
        <div class="space-y-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <x-gui::breadcrumbs :items="['Workspace' => '/catalog/examples/dashboard', 'Customers' => null]" />
                {{-- Focusable so focus has somewhere to go after deleting from a row's menu. --}}
                <h1 id="customers-heading" tabindex="-1" class="mt-3 text-4xl font-normal tracking-tight focus:outline-none sm:text-5xl">All <span class="text-zinc-500 dark:text-zinc-400">customers</span></h1>
                <p class="mt-3 text-zinc-600 dark:text-zinc-400">Everyone with an Acme subscription.</p>
            </div>
            <x-gui::button color="ink" shape="pill" size="sm" x-on:click="GiraffeUI.toast('Customers exported', { color: 'success' })">
                Export <x-gui::icon name="arrow-down" size="sm" />
            </x-gui::button>
        </div>

        <div class="rounded-3xl bg-zinc-100 p-4 sm:p-6 dark:bg-zinc-900">
            <livewire:customers-table />
        </div>
        </div>
    </x-example::app>
@endsection
