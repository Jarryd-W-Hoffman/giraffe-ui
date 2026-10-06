@extends('catalog::layout')

@section('content')
    @php
        // Built from the catalog itself, so this page can't fall behind what exists.
        $groups = collect(\Workbench\App\Catalog::components())->groupBy(fn ($component) => $component['group'] ?? 'Components', preserveKeys: true);
        $order = ['Actions', 'Forms', 'Display', 'Charts', 'Navigation', 'Overlays', 'Disclosure', 'Data'];
        $groups = $groups->sortBy(fn ($components, $group) => array_search($group, $order, true) === false ? 99 : array_search($group, $order, true));
        $count = $groups->flatten(1)->count();
        $examples = \Workbench\App\Catalog::examples();
    @endphp

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
            'Platform first' => 'Native <dialog>, popovers, CSS anchor positioning and invoker commands do the work. Alpine fills the gaps, and nothing needs Livewire.',
            'Accessible by default' => 'Real buttons and links, visible focus, ARIA where it matters, and full keyboard support. Every page is checked against WCAG 2.2 AA.',
            'Themed with CSS' => 'Colours and radius are Tailwind v4 theme variables. Override them in your CSS. No PHP config.',
        ] as $term => $description)
            <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-800">
                <dt class="font-semibold">{{ $term }}</dt>
                <dd class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ $description }}</dd>
            </div>
        @endforeach
    </dl>

    <h2 class="mt-14 text-xl font-semibold tracking-tight">{{ $count }} components</h2>
    <dl class="mt-4 space-y-4">
        @foreach ($groups as $group => $components)
            <div class="sm:grid sm:grid-cols-[8rem_1fr] sm:gap-4">
                <dt class="text-sm font-semibold">{{ $group }}</dt>
                <dd class="mt-1 text-sm text-zinc-600 sm:mt-0 dark:text-zinc-400">
                    @foreach ($components as $slug => $component)
                        <a href="/catalog/{{ $slug }}" class="font-medium text-gui-primary-text hover:underline">{{ $component['name'] }}</a>{{ $loop->last ? '' : ', ' }}
                    @endforeach
                </dd>
            </div>
        @endforeach
    </dl>

    <h2 class="mt-14 text-xl font-semibold tracking-tight">Full-page examples</h2>
    <p class="mt-3 text-zinc-600 dark:text-zinc-400">Whole pages built only from GiraffeUI components, to see how they fit together.</p>
    <ul class="mt-4 flex flex-wrap gap-2">
        @foreach ($examples as $slug => $title)
            <li><x-gui::button href="/catalog/examples/{{ $slug }}" variant="outline" color="secondary" size="sm">{{ $title }}</x-gui::button></li>
        @endforeach
    </ul>

    <h2 class="mt-14 text-xl font-semibold tracking-tight">Status</h2>
    <p class="mt-3 text-zinc-600 dark:text-zinc-400">
        GiraffeUI is pre-1.0, so expect breaking changes between minor versions. Every component works with plain Blade and Alpine, and with Livewire, and is tested in Chromium, Firefox and WebKit (Safari's engine), in light and dark mode. See the <a href="https://github.com/Jarryd-W-Hoffman/GiraffeUI/blob/master/CHANGELOG.md" class="font-medium text-gui-primary-text hover:underline">changelog</a> for what's new.
    </p>
@endsection
