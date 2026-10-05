@extends('catalog::examples.layout')

@section('content')
    <x-example::app-shell>
        <div class="mx-auto max-w-6xl space-y-6">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    {{-- Focusable so focus has somewhere to go after deleting from a row's menu. --}}
                    <h1 id="customers-heading" tabindex="-1" class="text-2xl font-semibold tracking-tight focus:outline-none">Customers</h1>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Everyone with an Acme subscription.</p>
                </div>
                <x-gui::button size="sm" x-on:click="GiraffeUI.toast('Customers exported', { color: 'success' })">
                    <x-gui::icon name="arrow-down" size="sm" />
                    Export
                </x-gui::button>
            </div>

            <livewire:customers-table />
        </div>
    </x-example::app-shell>
@endsection
