@extends('catalog::layout')

@section('content')
    <h1 class="text-3xl font-bold tracking-tight">GiraffeUI</h1>
    <p class="mt-3 text-lg text-zinc-600 dark:text-zinc-400">
        Free, accessible Blade + Alpine components for Laravel and Tailwind CSS v4. Works great with Livewire, but never requires it.
    </p>

    <div class="mt-8 flex flex-wrap gap-3">
        <x-gui::button href="/catalog/installation">Get started</x-gui::button>
        <x-gui::button href="/catalog/button" variant="outline" color="secondary">Browse components</x-gui::button>
    </div>

    <h2 class="mt-14 text-xl font-semibold tracking-tight">Principles</h2>
    <dl class="mt-4 grid gap-4 sm:grid-cols-2">
        @foreach ([
            'Free, all of it' => 'Every component is MIT licensed. No paid tier hiding the good stuff.',
            'Blade first' => 'Plain Blade components with Alpine for interactivity. Livewire works, but is optional.',
            'Accessible by default' => 'Real buttons and links, visible focus, ARIA where it matters, and keyboard support.',
            'Themed with CSS' => 'Colours and radius are Tailwind v4 theme variables. Override them in your CSS. No PHP config.',
        ] as $term => $description)
            <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-800">
                <dt class="font-semibold">{{ $term }}</dt>
                <dd class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ $description }}</dd>
            </div>
        @endforeach
    </dl>

    <h2 class="mt-14 text-xl font-semibold tracking-tight">Status</h2>
    <p class="mt-3 text-zinc-600 dark:text-zinc-400">
        GiraffeUI is pre-1.0, so expect breaking changes between minor versions. Forms are next, then interactive components (modal, dropdown, tabs), then richer tables.
    </p>
@endsection
