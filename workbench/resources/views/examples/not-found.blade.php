@extends('catalog::examples.layout')

@section('content')
    <main class="flex min-h-dvh flex-col items-center justify-center bg-white px-4 py-12 text-center dark:bg-black">
        <x-example::brand class="mb-12" />
        {{-- The number as a quiet gradient, the way the dashboard's headline greys out its second half. --}}
        <p class="bg-linear-to-br from-lime-500 to-violet-600 bg-clip-text text-8xl font-semibold tracking-tighter text-transparent sm:text-9xl dark:from-lime-300 dark:to-violet-500" aria-hidden="true">404</p>
        <h1 class="mt-4 text-4xl font-normal tracking-tight sm:text-5xl">Page <span class="text-zinc-500 dark:text-zinc-400">not found</span></h1>
        <p class="mt-4 max-w-md text-zinc-600 dark:text-zinc-400">The page you're looking for has moved or never existed. Try searching, or head back to the dashboard.</p>

        <form action="#" role="search" class="mt-8 w-full max-w-sm" onsubmit="event.preventDefault(); GiraffeUI.toast('Nothing found for that, either.')">
            <x-gui::input type="search" name="q" placeholder="Search the docs" aria-label="Search the docs">
                <x-slot:leading>
                    <x-gui::icon name="magnifying-glass" size="sm" />
                </x-slot:leading>
            </x-gui::input>
        </form>

        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
            <x-gui::button color="ink" shape="pill" href="/catalog/examples/dashboard">Go to the dashboard</x-gui::button>
            <x-gui::button variant="ghost" color="ink" shape="pill" href="#">Contact support <x-gui::icon name="arrow-right" size="sm" /></x-gui::button>
        </div>
    </main>
@endsection
