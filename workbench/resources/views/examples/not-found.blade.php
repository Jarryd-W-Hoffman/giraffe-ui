@extends('catalog::examples.layout')

@section('content')
    <main class="flex min-h-dvh flex-col items-center justify-center px-4 py-12 text-center">
        <p class="text-sm font-semibold text-gui-primary-text">404</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">Page not found</h1>
        <p class="mt-3 max-w-md text-zinc-600 dark:text-zinc-400">The page you're looking for has moved or never existed. Try searching, or head back to the dashboard.</p>

        <form action="#" role="search" class="mt-8 w-full max-w-sm" onsubmit="event.preventDefault(); GiraffeUI.toast('Nothing found for that, either.')">
            <x-gui::input type="search" name="q" placeholder="Search the docs" aria-label="Search the docs">
                <x-slot:leading>
                    <x-gui::icon name="magnifying-glass" size="sm" />
                </x-slot:leading>
            </x-gui::input>
        </form>

        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
            <x-gui::button href="/catalog/examples/dashboard">Go to the dashboard</x-gui::button>
            <x-gui::button href="#" variant="ghost" color="secondary">Contact support <x-gui::icon name="arrow-right" size="sm" /></x-gui::button>
        </div>
    </main>
@endsection
