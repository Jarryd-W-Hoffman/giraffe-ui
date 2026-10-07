<!DOCTYPE html>
<html lang="en">
    <head>
        @include('catalog::partials.head')

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('playground', ({ slug, controls, selfClosing }) => ({
                    controls,
                    state: Object.fromEntries(controls.map((control) => [control.name, control.default])),
                    request: 0,

                    init() {
                        this.$watch('state', () => this.refresh());
                    },

                    async refresh() {
                        const id = ++this.request;
                        const params = new URLSearchParams(Object.entries(this.state).map(([key, value]) => [key, String(value)]));
                        const response = await fetch(`/catalog/preview/${slug}?${params}`);
                        const html = await response.text();

                        // Ignore responses that arrive after a newer request.
                        if (id === this.request) {
                            this.$refs.preview.innerHTML = html;
                        }
                    },

                    reset() {
                        this.state = Object.fromEntries(controls.map((control) => [control.name, control.default]));
                    },

                    get snippet() {
                        const escape = (value) => String(value).replaceAll('"', '&quot;');
                        const attributes = controls
                            .filter((control) => ! control.slot)
                            .map((control) => {
                                const value = this.state[control.name];

                                if (control.type === 'boolean') {
                                    return value ? (control.attribute ?? control.name) : null;
                                }

                                // "always" is for controls whose starting value isn't the component's own default.
                                const include = value !== '' && (control.always || value !== control.default);

                                return include ? `${control.name}="${escape(value)}"` : null;
                            })
                            .filter(Boolean);

                        const slot = controls.find((control) => control.slot);
                        const content = slot ? this.state[slot.name] : '';

                        const tag = `x-gui::${slug}${attributes.length ? ' ' + attributes.join(' ') : ''}`;

                        return selfClosing ? `<${tag} />` : `<${tag}>${content}</x-gui::${slug}>`;
                    },
                }));

                Alpine.data('themer', () => ({
                    ...Catalog.load(),

                    init() {
                        this.$watch('primary', () => this.save());
                        this.$watch('radius', () => this.save());
                    },

                    save() {
                        Catalog.save({ primary: this.primary, radius: this.radius });
                    },

                    reset() {
                        Object.assign(this, Catalog.defaults);
                    },

                    get snippet() {
                        const theme = [];

                        if (this.primary !== Catalog.defaults.primary) {
                            theme.push(`    --color-gui-primary: var(--color-${this.primary}-600);`);
                        }

                        if (this.radius !== Catalog.defaults.radius) {
                            theme.push(`    --radius-gui: ${Catalog.radiusValue(this.radius)};`);
                        }

                        if (! theme.length) {
                            return '/* You are using the default theme. Pick a colour or radius to see the overrides. */';
                        }

                        let css = `@theme {\n${theme.join('\n')}\n}`;

                        if (this.primary !== Catalog.defaults.primary) {
                            css += `\n\n/* A lighter shade for primary text in dark mode. */\n@layer base {\n    :root {\n        @variant dark {\n            --color-gui-primary-text: var(--color-${this.primary}-400);\n        }\n    }\n}`;
                        }

                        return css;
                    },
                }));
            });
        </script>

        {{-- Alpine comes from Livewire's injected script, or from the head partial when Livewire isn't installed. --}}
    </head>
    <body class="min-h-screen bg-white text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100" x-data="{ menu: false }">
        <header class="sticky top-0 z-30 border-b border-zinc-200 bg-white/80 backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/80">
            <div class="mx-auto flex h-14 max-w-7xl items-center gap-3 px-4 md:px-6">
                <button
                    type="button"
                    class="-ms-1 rounded-md p-1.5 text-zinc-600 hover:bg-zinc-100 md:hidden dark:text-zinc-400 dark:hover:bg-zinc-900"
                    x-on:click="menu = ! menu"
                    x-bind:aria-expanded="menu"
                    aria-controls="sidebar"
                    aria-label="Toggle navigation"
                >
                    <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M2 4.75A.75.75 0 0 1 2.75 4h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 4.75ZM2 10a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 10Zm0 5.25a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" /></svg>
                </button>

                <a href="/catalog" class="flex items-center gap-2 font-semibold tracking-tight">
                    @include('catalog::partials.logo', ['class' => 'size-8'])
                    GiraffeUI
                </a>
                <span class="rounded-full border border-zinc-200 px-2 py-0.5 text-xs text-zinc-500 dark:border-zinc-800 dark:text-zinc-400">pre-1.0</span>

                <nav class="ms-auto flex items-center gap-1">
                    <button
                        type="button"
                        commandfor="catalog-search"
                        command="show-modal"
                        class="me-1 flex cursor-pointer items-center gap-2 rounded-gui border border-zinc-200 px-2.5 py-1.5 text-sm text-zinc-600 hover:border-zinc-300 hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-gui-primary-text dark:border-zinc-800 dark:text-zinc-400 dark:hover:border-zinc-700 dark:hover:text-zinc-100"
                    >
                        <x-gui::icon name="magnifying-glass" size="sm" />
                        <span>Search</span>
                        <span class="hidden gap-0.5 sm:flex" aria-hidden="true"><x-gui::kbd>⌘</x-gui::kbd><x-gui::kbd>K</x-gui::kbd></span>
                    </button>

                    <a href="https://github.com/Jarryd-W-Hoffman/GiraffeUI" class="hidden rounded-md px-3 py-1.5 text-sm text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 sm:block dark:text-zinc-400 dark:hover:bg-zinc-900 dark:hover:text-zinc-100">GitHub</a>

                    <x-gui::theme-switcher variant="toggle" size="sm" />
                </nav>
            </div>
        </header>

        <div class="mx-auto flex max-w-7xl">
            <div x-show="menu" x-cloak x-transition.opacity class="fixed inset-0 top-14 z-20 bg-zinc-950/40 md:hidden" x-on:click="menu = false"></div>

            <aside
                id="sidebar"
                class="fixed inset-y-0 top-14 start-0 z-20 w-64 -translate-x-full overflow-y-auto border-e border-zinc-200 bg-white px-4 py-8 transition-transform md:sticky md:block md:h-[calc(100vh-3.5rem)] md:translate-x-0 md:border-e-0 md:bg-transparent md:px-6 dark:border-zinc-800 dark:bg-zinc-950 md:dark:bg-transparent"
                x-bind:class="menu && 'translate-x-0'"
            >
                @foreach (collect($nav)->groupBy('group') as $group => $items)
                    <p class="mb-2 px-2 text-xs font-semibold tracking-wide text-zinc-500 uppercase dark:text-zinc-400">{{ $group }}</p>
                    <ul class="mb-8 space-y-0.5">
                        @foreach ($items as $item)
                            <li>
                                <a
                                    href="/catalog/{{ $item['slug'] }}"
                                    @class([
                                        'block rounded-md px-2 py-1.5 text-sm transition-colors',
                                        'bg-zinc-100 font-medium text-zinc-900 dark:bg-zinc-900 dark:text-zinc-100' => $item['slug'] === $current,
                                        'text-zinc-600 hover:bg-zinc-50 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-900/60 dark:hover:text-zinc-100' => $item['slug'] !== $current,
                                    ])
                                    @if ($item['slug'] === $current) aria-current="page" @endif
                                >{{ $item['title'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            </aside>

            <main class="min-w-0 flex-1 px-4 py-10 md:px-8">
                <div class="flex gap-12">
                    <article class="min-w-0 max-w-3xl flex-1">
                        @yield('content')

                        <nav class="mt-16 flex items-center justify-between gap-4 border-t border-zinc-200 pt-6 dark:border-zinc-800" aria-label="Pagination">
                            @if ($previous)
                                <x-gui::button href="/catalog/{{ $previous['slug'] }}" variant="outline" color="secondary" size="sm">← {{ $previous['title'] }}</x-gui::button>
                            @else
                                <span></span>
                            @endif
                            @if ($next)
                                <x-gui::button href="/catalog/{{ $next['slug'] }}" variant="outline" color="secondary" size="sm">{{ $next['title'] }} →</x-gui::button>
                            @endif
                        </nav>
                    </article>

                    @hasSection('toc')
                        <aside class="sticky top-24 hidden h-fit w-48 shrink-0 xl:block">
                            <p class="mb-3 text-sm font-medium">On this page</p>
                            <ul class="space-y-2 text-sm text-zinc-600 dark:text-zinc-400">
                                @yield('toc')
                            </ul>
                        </aside>
                    @endif
                </div>
            </main>
        </div>

        <x-gui::command-palette id="catalog-search" placeholder="Search the catalog" label="Search the catalog">
            @foreach (collect($nav)->groupBy('group') as $group => $items)
                <x-gui::command-palette.group :heading="$group">
                    @foreach ($items as $item)
                        <x-gui::command-palette.item :href="'/catalog/'.$item['slug']" :keywords="$item['slug']">{{ $item['title'] }}</x-gui::command-palette.item>
                    @endforeach
                </x-gui::command-palette.group>
            @endforeach
            <x-gui::command-palette.group heading="Theme">
                <x-gui::command-palette.item x-on:click="GiraffeUI.setTheme('light')" keywords="appearance">Light mode</x-gui::command-palette.item>
                <x-gui::command-palette.item x-on:click="GiraffeUI.setTheme('dark')" keywords="appearance">Dark mode</x-gui::command-palette.item>
                <x-gui::command-palette.item x-on:click="GiraffeUI.setTheme('system')" keywords="appearance os">System theme</x-gui::command-palette.item>
            </x-gui::command-palette.group>
        </x-gui::command-palette>

        <x-gui::toasts />
    </body>
</html>
