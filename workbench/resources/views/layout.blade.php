<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title }} · GiraffeUI</title>
        <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='6' fill='%234f46e5'/%3E%3Ctext x='16' y='23' font-family='system-ui,sans-serif' font-size='20' font-weight='700' fill='white' text-anchor='middle'%3EG%3C/text%3E%3C/svg%3E">

        <script>
            // Same dark mode helper the published layout ships with.
            window.GiraffeUI = {
                setTheme(theme) {
                    try {
                        theme === 'system' ? localStorage.removeItem('theme') : localStorage.setItem('theme', theme);
                    } catch (e) {}
                    this.applyTheme();
                },
                applyTheme() {
                    let saved = null;
                    try { saved = localStorage.getItem('theme'); } catch (e) {}
                    const dark = saved ? saved === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
                    document.documentElement.classList.toggle('dark', dark);
                },
            };
            GiraffeUI.applyTheme();

            // The theme customiser's choices, kept across catalog pages.
            window.Catalog = {
                defaults: { primary: 'indigo', radius: 'md' },
                load() {
                    try {
                        return { ...this.defaults, ...JSON.parse(localStorage.getItem('catalog-theme') || '{}') };
                    } catch (e) {
                        return { ...this.defaults };
                    }
                },
                save(theme) {
                    try { localStorage.setItem('catalog-theme', JSON.stringify(theme)); } catch (e) {}
                    this.apply(theme);
                },
                radiusValue(radius) {
                    return radius === 'none' ? '0px' : `var(--radius-${radius})`;
                },
                apply(theme) {
                    // A <style> tag rather than inline styles, so the dark-mode text shade can apply too.
                    let style = document.getElementById('catalog-theme');

                    if (! style) {
                        style = document.createElement('style');
                        style.id = 'catalog-theme';
                        document.head.appendChild(style);
                    }

                    const rules = [];

                    if (theme.primary !== this.defaults.primary) {
                        rules.push(`:root { --color-gui-primary: var(--color-${theme.primary}-600); }`);
                        rules.push(`:root.dark { --color-gui-primary-text: var(--color-${theme.primary}-400); }`);
                    }

                    if (theme.radius !== this.defaults.radius) {
                        rules.push(`:root { --radius-gui: ${this.radiusValue(theme.radius)}; }`);
                    }

                    style.textContent = rules.join('\n');
                },
            };
            Catalog.apply(Catalog.load());

            document.addEventListener('alpine:init', () => {
                Alpine.data('playground', ({ slug, controls }) => ({
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
                                    return value ? control.name : null;
                                }

                                return value !== '' && value !== control.default ? `${control.name}="${escape(value)}"` : null;
                            })
                            .filter(Boolean);

                        const slot = controls.find((control) => control.slot);
                        const content = slot ? this.state[slot.name] : '';

                        return `<x-gui::${slug}${attributes.length ? ' ' + attributes.join(' ') : ''}>${content}</x-gui::${slug}>`;
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

        <link rel="stylesheet" href="/catalog/assets/catalog.css?v={{ time() }}">
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js"></script>
    </head>
    <body class="min-h-screen bg-white text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100" x-data="{ menu: false }">
        <header class="sticky top-0 z-30 border-b border-zinc-200 bg-white/80 backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/80">
            <div class="mx-auto flex h-14 max-w-7xl items-center gap-3 px-4 md:px-6">
                <button
                    type="button"
                    class="-ml-1 rounded-md p-1.5 text-zinc-600 hover:bg-zinc-100 md:hidden dark:text-zinc-400 dark:hover:bg-zinc-900"
                    x-on:click="menu = ! menu"
                    x-bind:aria-expanded="menu"
                    aria-controls="sidebar"
                    aria-label="Toggle navigation"
                >
                    <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M2 4.75A.75.75 0 0 1 2.75 4h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 4.75ZM2 10a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 10Zm0 5.25a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" /></svg>
                </button>

                <a href="/catalog" class="flex items-center gap-2 font-semibold tracking-tight">
                    <span class="grid size-7 place-items-center rounded-gui bg-gui-primary text-sm font-bold text-gui-primary-foreground">G</span>
                    GiraffeUI
                </a>
                <span class="rounded-full border border-zinc-200 px-2 py-0.5 text-xs text-zinc-500 dark:border-zinc-800 dark:text-zinc-400">pre-1.0</span>

                <nav class="ml-auto flex items-center gap-1">
                    <a href="https://github.com/Jarryd-W-Hoffman/GiraffeUI" class="hidden rounded-md px-3 py-1.5 text-sm text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 sm:block dark:text-zinc-400 dark:hover:bg-zinc-900 dark:hover:text-zinc-100">GitHub</a>

                    <button
                        type="button"
                        class="rounded-md p-2 text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-900 dark:hover:text-zinc-100"
                        x-data
                        x-on:click="GiraffeUI.setTheme(document.documentElement.classList.contains('dark') ? 'light' : 'dark')"
                        aria-label="Toggle dark mode"
                    >
                        <svg class="size-4 dark:hidden" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.455 2.004a.75.75 0 0 1 .26.77 7 7 0 0 0 9.958 7.967.75.75 0 0 1 1.067.853A8.5 8.5 0 1 1 6.647 1.921a.75.75 0 0 1 .808.083Z" clip-rule="evenodd" /></svg>
                        <svg class="hidden size-4 dark:block" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 2a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 2ZM10 15a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 15ZM10 7a3 3 0 1 0 0 6 3 3 0 0 0 0-6ZM15.657 5.404a.75.75 0 1 0-1.06-1.06l-1.061 1.06a.75.75 0 0 0 1.06 1.06l1.06-1.06ZM6.464 14.596a.75.75 0 1 0-1.06-1.06l-1.06 1.06a.75.75 0 0 0 1.06 1.06l1.06-1.06ZM18 10a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5A.75.75 0 0 1 18 10ZM5 10a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5A.75.75 0 0 1 5 10ZM14.596 15.657a.75.75 0 0 0 1.06-1.06l-1.06-1.061a.75.75 0 1 0-1.06 1.06l1.06 1.06ZM5.404 6.464a.75.75 0 0 0 1.06-1.06l-1.06-1.06a.75.75 0 1 0-1.061 1.06l1.06 1.06Z" /></svg>
                    </button>
                </nav>
            </div>
        </header>

        <div class="mx-auto flex max-w-7xl">
            <div x-show="menu" x-cloak x-transition.opacity class="fixed inset-0 top-14 z-20 bg-zinc-950/40 md:hidden" x-on:click="menu = false"></div>

            <aside
                id="sidebar"
                class="fixed inset-y-0 top-14 left-0 z-20 w-64 -translate-x-full overflow-y-auto border-r border-zinc-200 bg-white px-4 py-8 transition-transform md:sticky md:block md:h-[calc(100vh-3.5rem)] md:translate-x-0 md:border-r-0 md:bg-transparent md:px-6 dark:border-zinc-800 dark:bg-zinc-950 md:dark:bg-transparent"
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
    </body>
</html>
