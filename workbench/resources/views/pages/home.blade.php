{{--
    GiraffeUI's landing page. Everything interactive on it is a real GiraffeUI component: the forms, charts, switches,
    the theming playground and the ⌘K search. It has its own layout rather than the catalog's sidebar.
--}}
@php
    $components = \Workbench\App\Catalog::components();
    $componentCount = count($components);
    $iconCount = count(\JayAitch\GiraffeUI\Support\Icons::PATHS);
    $install = 'composer require jayaitch/giraffeui';

    $features = [
        ['icon' => 'check-badge', 'title' => 'Accessible by default', 'text' => 'Built to WCAG 2.2 AA and the WAI-ARIA patterns, with keyboard support, focus management and labels done for you. Every page is audited with axe in light and dark mode.'],
        ['icon' => 'code-bracket', 'title' => 'Just Blade and Alpine', 'text' => 'No React, no Vue, no build step for your views. Components are Blade tags; Alpine adds behaviour where the platform can\'t.'],
        ['icon' => 'bolt', 'title' => 'Ready for Livewire', 'text' => 'wire:model, wire:click and loading states work out of the box. Livewire is welcome, never required: CI proves every component runs without it.'],
        ['icon' => 'squares-2x2', 'title' => 'Platform first', 'text' => 'Native dialog, popover, details and form validation, with CSS anchor positioning. Less JavaScript, fewer bugs, faster pages.'],
        ['icon' => 'swatch', 'title' => 'Yours to theme', 'text' => 'Tailwind CSS v4 tokens for colour and radius. Change one variable and every component follows, in light and dark mode.'],
        ['icon' => 'presentation-chart-line', 'title' => 'Charts included', 'text' => 'Line, area, bar, donut, scatter and heatmap charts drawn on the server, with colour-blind-safe colours, keyboard tooltips and data tables.'],
    ];

    $colors = ['indigo', 'violet', 'blue', 'sky', 'teal', 'emerald', 'rose', 'orange'];
    $radii = ['none' => '0px', 'sm' => 'var(--radius-sm)', 'md' => 'var(--radius-md)', 'lg' => 'var(--radius-lg)', 'xl' => 'var(--radius-xl)', 'full' => '9999px'];

    // Customers is a Livewire component, so without Livewire its place goes to Onboarding.
    $examples = array_values(array_filter([
        ['slug' => 'dashboard', 'title' => 'Dashboard', 'text' => 'Charts, stats and a collapsible sidebar.'],
        ['slug' => 'projects', 'title' => 'Projects', 'text' => 'A board of tasks in a workspace.'],
        ['slug' => 'inbox', 'title' => 'Inbox', 'text' => 'Conversations, search and replies.'],
        \Workbench\App\Catalog::livewire()
            ? ['slug' => 'customers', 'title' => 'Customers', 'text' => 'A sortable, filterable table, with Livewire.']
            : ['slug' => 'onboarding', 'title' => 'Onboarding', 'text' => 'A four-step setup, as plain form posts.'],
        ['slug' => 'sign-in', 'title' => 'Sign in', 'text' => 'A split page with validation.'],
        ['slug' => 'pricing', 'title' => 'Pricing', 'text' => 'Plans, a billing switch and FAQs.'],
    ]));

    $modalCode = <<<'BLADE'
        <x-gui::button commandfor="invite" command="show-modal">
            Invite a teammate
        </x-gui::button>

        <x-gui::modal id="invite" title="Invite a teammate">
            <x-gui::field label="Email" name="email" required>
                <x-gui::input type="email" />
            </x-gui::field>

            <x-slot:footer>
                <x-gui::modal.close>Cancel</x-gui::modal.close>
                <x-gui::button>Send invite</x-gui::button>
            </x-slot:footer>
        </x-gui::modal>
        BLADE;

    $livewireCode = <<<'BLADE'
        <x-gui::field label="Search customers" name="search">
            <x-gui::input wire:model.live.debounce="search" />
        </x-gui::field>

        <x-gui::button wire:click="export">
            Export
        </x-gui::button>
        BLADE;

    $footer = [
        'Docs' => ['Introduction' => '/catalog/introduction', 'Installation' => '/catalog/installation', 'Theming' => '/catalog/theming'],
        'Components' => ['Button' => '/catalog/button', 'Modal' => '/catalog/modal', 'Date picker' => '/catalog/date-picker', 'Line chart' => '/catalog/line-chart'],
        'Examples' => ['Dashboard' => '/catalog/examples/dashboard', 'Projects' => '/catalog/examples/projects', 'Pricing' => '/catalog/examples/pricing'],
    ];
@endphp
<!DOCTYPE html>
<html lang="en">
    <head>
        @include('catalog::partials.head')
    </head>
    <body class="bg-white text-zinc-900 antialiased dark:bg-black dark:text-zinc-100" x-data>
        <x-gui::navbar sticky container="max-w-7xl" class="border-transparent! bg-white/80! dark:bg-black/80!">
            <x-slot:brand>
                <a href="/catalog/home" class="flex items-center gap-2.5 rounded-full text-lg font-semibold tracking-tight focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-gui-primary-text">
                    @include('catalog::partials.logo', ['class' => 'size-9'])
                    GiraffeUI
                </a>
            </x-slot:brand>

            <x-gui::navbar.item href="/catalog/introduction">Docs</x-gui::navbar.item>
            <x-gui::navbar.item href="/catalog/button">Components</x-gui::navbar.item>
            <x-gui::navbar.item href="/catalog/examples/dashboard">Examples</x-gui::navbar.item>
            <x-gui::navbar.item href="https://github.com/Jarryd-W-Hoffman/GiraffeUI">GitHub</x-gui::navbar.item>

            <x-slot:actions>
                <button
                    type="button"
                    commandfor="home-search"
                    command="show-modal"
                    class="hidden cursor-pointer items-center gap-2 rounded-full bg-zinc-100 py-1.5 ps-3 pe-1.5 text-sm text-zinc-600 transition-colors hover:bg-zinc-200 hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-gui-primary-text sm:flex dark:bg-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100"
                >
                    <x-gui::icon name="magnifying-glass" size="sm" />
                    Search components
                    <span class="flex gap-0.5" aria-hidden="true"><x-gui::kbd>⌘</x-gui::kbd><x-gui::kbd>K</x-gui::kbd></span>
                </button>
                <x-gui::theme-switcher variant="toggle" size="sm" />
                <x-gui::button href="/catalog/installation" color="ink" shape="pill" size="sm" class="max-sm:hidden">Get started</x-gui::button>
            </x-slot:actions>
        </x-gui::navbar>

        <main>
            {{-- The hero: a glow, a fine grid, and the one command to install. --}}
            <section class="relative isolate overflow-hidden px-4 pt-20 pb-16 sm:px-6 sm:pt-28">
                <div class="pointer-events-none absolute inset-0 -z-10" aria-hidden="true">
                    <div class="absolute top-0 left-1/2 h-[36rem] w-[64rem] -translate-x-1/2 rounded-full bg-linear-to-r from-lime-300/40 via-violet-400/30 to-violet-600/40 opacity-70 blur-3xl dark:from-lime-400/20 dark:via-violet-500/20 dark:to-violet-700/30"></div>
                    <div class="absolute inset-0 bg-[linear-gradient(to_right,var(--color-zinc-200)_1px,transparent_1px),linear-gradient(to_bottom,var(--color-zinc-200)_1px,transparent_1px)] bg-size-[3.5rem_3.5rem] [mask-image:radial-gradient(ellipse_at_top,black_30%,transparent_70%)] opacity-60 dark:bg-[linear-gradient(to_right,var(--color-zinc-800)_1px,transparent_1px),linear-gradient(to_bottom,var(--color-zinc-800)_1px,transparent_1px)]"></div>
                </div>

                <div class="mx-auto max-w-4xl text-center">
                    <a href="/catalog/line-chart" class="group inline-flex items-center gap-2 rounded-full bg-white/70 py-1 ps-1 pe-3 text-sm ring-1 ring-zinc-200 backdrop-blur transition hover:ring-zinc-300 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text dark:bg-zinc-900/70 dark:ring-zinc-800 dark:hover:ring-zinc-700">
                        <x-gui::badge shape="pill" size="sm" variant="solid" class="bg-lime-300! text-zinc-900!">New</x-gui::badge>
                        Charts, a collapsible sidebar and {{ $iconCount }} icons
                        <x-gui::icon name="arrow-right" size="xs" class="transition-transform group-hover:translate-x-0.5" />
                    </a>

                    <h1 class="mt-8 text-5xl font-normal tracking-tight text-balance sm:text-7xl">
                        Beautiful,
                        <span class="bg-linear-to-r from-lime-600 to-violet-600 bg-clip-text text-transparent dark:from-lime-300 dark:to-violet-400">accessible</span>
                        components <span class="text-zinc-500 dark:text-zinc-400">for Laravel</span>
                    </h1>
                    <p class="mx-auto mt-6 max-w-2xl text-lg text-pretty text-zinc-600 dark:text-zinc-400">
                        {{ $componentCount }} Blade and Alpine components for Laravel and Tailwind CSS v4. Built on native HTML, tested in Chrome, Firefox and Safari, and ready for Livewire without ever needing it.
                    </p>

                    <div class="mt-10 flex flex-wrap items-center justify-center gap-3">
                        <x-gui::button href="/catalog/installation" color="ink" shape="pill" size="lg">
                            Get started <x-gui::icon name="arrow-right" size="sm" />
                        </x-gui::button>
                        <x-gui::button href="/catalog/introduction" variant="outline" color="ink" shape="pill" size="lg">Browse components</x-gui::button>
                    </div>

                    <div class="mx-auto mt-8 flex w-fit max-w-full items-center gap-3 rounded-full bg-zinc-100 py-1.5 ps-5 pe-1.5 font-mono text-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-zinc-800">
                        <span class="text-zinc-400 select-none dark:text-zinc-500" aria-hidden="true">$</span>
                        <code class="truncate">{{ $install }}</code>
                        <x-gui::copy-button :value="$install" label="Copy the install command" class="[&_button]:rounded-full" />
                    </div>
                </div>
            </section>

            {{-- A wall of real, working components. --}}
            <section aria-labelledby="showcase-heading" class="px-4 pb-24 sm:px-6">
                <h2 id="showcase-heading" class="sr-only">Components in action</h2>
                <div class="mx-auto grid max-w-7xl gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div class="space-y-4">
                        <x-gui::card variant="filled" title="Create an account" icon="user-plus">
                            <form class="space-y-4" x-on:submit.prevent="GiraffeUI.toast('Welcome aboard!', { color: 'success' })">
                                <x-gui::field label="Email" name="showcase_email">
                                    <x-gui::input type="email" placeholder="ada@example.com" autocomplete="off" />
                                </x-gui::field>
                                <x-gui::field label="Password" name="showcase_password">
                                    <x-gui::input type="password" viewable autocomplete="new-password" />
                                </x-gui::field>
                                <x-gui::checkbox name="showcase_terms" label="Send me product news" />
                                <x-gui::button type="submit" color="ink" shape="pill" class="w-full">Create account</x-gui::button>
                            </form>
                        </x-gui::card>

                        <x-gui::card variant="filled" title="Verify it's you" icon="shield-check">
                            <x-gui::field label="Code" name="showcase_code" hint="We sent it to your phone. Try pasting it.">
                                <x-gui::otp-input x-on:change="$event.target.value.length === 6 && GiraffeUI.toast('Code accepted', { color: 'success' })" />
                            </x-gui::field>
                        </x-gui::card>

                        <x-gui::card variant="filled" title="How was your order?" icon="star">
                            <form class="space-y-4" x-on:submit.prevent="GiraffeUI.toast('Thanks for the feedback!', { color: 'success' })">
                                <x-gui::rating name="showcase_rating" value="4" aria-label="Rating" />
                                <x-gui::field label="Anything to add?" name="showcase_feedback">
                                    <x-gui::textarea rows="2" autosize counter maxlength="140" placeholder="It arrived a day early…" />
                                </x-gui::field>
                                <x-gui::button type="submit" variant="ghost" color="ink" shape="pill">Send feedback</x-gui::button>
                            </form>
                        </x-gui::card>
                    </div>

                    <div class="space-y-4">
                        <x-gui::card variant="filled">
                            <div class="flex items-start justify-between gap-4">
                                <x-gui::stat label="Revenue" value="$48,200" change="12%" trend="up" description="vs last month" />
                                <div style="--radius-gui: 9999px">
                                    <x-gui::segmented-control name="showcase_period" label="Period" size="sm" :options="['7d' => '7d', '30d' => '30d', '12m' => '12m']" value="30d" />
                                </div>
                            </div>
                            <x-gui::chart.line
                                class="mt-4"
                                :labels="['Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct']"
                                :series="['This year' => [31, 34, 33, 38, 37, 41, 43, 48.2], 'Last year' => [27, 29, 28, 30, 32, 31, 33, 36]]"
                                :colors="['This year' => 'primary', 'Last year' => 'muted']"
                                :dashed="['Last year']"
                                :axis="false"
                                :height="140"
                                prefix="$"
                                suffix="K"
                                curve
                                area
                            />
                        </x-gui::card>

                        <x-gui::card variant="filled" title="Notifications" icon="bell">
                            <div class="space-y-4">
                                <x-gui::switch name="showcase_mentions" label="Mentions" description="When someone mentions you." checked />
                                <x-gui::switch name="showcase_orders" label="New orders" description="As they come in." />
                                <x-gui::switch name="showcase_digest" label="Weekly digest" description="Every Monday morning." checked />
                            </div>
                        </x-gui::card>

                        <x-gui::card variant="filled" title="Book a call" icon="calendar">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <x-gui::field label="Day" name="showcase_day">
                                    <x-gui::date-picker />
                                </x-gui::field>
                                <x-gui::field label="Time" name="showcase_time">
                                    <x-gui::time-picker :interval="30" min="09:00" max="17:00" />
                                </x-gui::field>
                            </div>
                            <x-gui::button color="ink" shape="pill" class="mt-4" x-on:click="GiraffeUI.toast('Call booked', { color: 'success' })">Book it</x-gui::button>
                        </x-gui::card>
                    </div>

                    <div class="space-y-4 md:col-span-2 lg:col-span-1">
                        <x-gui::card variant="filled" title="Team" icon="user-group">
                            <x-slot:actions>
                                <x-gui::button variant="ghost" color="ink" shape="pill" icon size="sm" aria-label="Invite a teammate" commandfor="home-invite" command="show-modal"><x-gui::icon name="plus" size="sm" /></x-gui::button>
                            </x-slot:actions>
                            <ul class="space-y-3">
                                @foreach ([['Ada Lovelace', 'Owner', 'primary'], ['Grace Hopper', 'Admin', 'info'], ['Alan Turing', 'Member', 'secondary']] as [$person, $role, $color])
                                    <li class="flex items-center gap-3">
                                        <x-gui::avatar :name="$person" size="sm" />
                                        <span class="min-w-0 flex-1 truncate text-sm font-medium">{{ $person }}</span>
                                        <x-gui::badge shape="pill" size="sm" :dot="$color">{{ $role }}</x-gui::badge>
                                    </li>
                                @endforeach
                            </ul>
                        </x-gui::card>

                        <x-gui::card variant="filled" title="Plan" icon="credit-card">
                            <div class="grid gap-3">
                                <x-gui::radio variant="card" name="showcase_plan" value="starter" label="Starter" description="$29 a month, one shop." />
                                <x-gui::radio variant="card" name="showcase_plan" value="pro" label="Pro" description="$99 a month, three shops." checked />
                            </div>
                            <div class="mt-5">
                                <x-gui::progress :value="72" label="Storage used" show-value />
                            </div>
                        </x-gui::card>

                        <x-gui::card variant="filled" title="Payment methods" icon="banknotes">
                            <x-gui::chart.donut :size="110" :thickness="12" :data="['Card' => 31200, 'PayPal' => 11400, 'Bank transfer' => 4100]" prefix="$" compact />
                        </x-gui::card>

                        <button
                            type="button"
                            commandfor="home-search"
                            command="show-modal"
                            class="group flex w-full cursor-pointer items-center justify-between gap-4 rounded-[calc(var(--radius-gui)+1.125rem)] bg-zinc-900 p-6 text-start text-white transition hover:bg-zinc-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200"
                        >
                            <span>
                                <span class="block text-lg">Find any component</span>
                                <span class="block text-sm text-zinc-400 dark:text-zinc-600">Press ⌘K anywhere on this page.</span>
                            </span>
                            <x-gui::icon name="magnifying-glass" class="transition-transform group-hover:scale-110" />
                        </button>
                    </div>
                </div>
            </section>

            {{-- The numbers. --}}
            <section aria-label="GiraffeUI in numbers" class="border-y border-zinc-200 px-4 py-12 sm:px-6 dark:border-zinc-800">
                <dl class="mx-auto grid max-w-7xl grid-cols-2 gap-8 text-center lg:grid-cols-4">
                    @foreach ([[$componentCount, 'components'], [$iconCount, 'icons, bundled'], ['3', 'browsers tested'], ['0', 'required build steps']] as [$number, $label])
                        <div>
                            <dt class="sr-only">{{ ucfirst($label) }}</dt>
                            <dd class="text-5xl font-normal tracking-tight tabular-nums">{{ $number }}</dd>
                            <dd class="mt-2 text-sm text-zinc-600 dark:text-zinc-400" aria-hidden="true">{{ $label }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>

            {{-- Why GiraffeUI. --}}
            <section aria-labelledby="features-heading" class="px-4 py-24 sm:px-6">
                <div class="mx-auto max-w-7xl">
                    <div class="max-w-2xl">
                        <h2 id="features-heading" class="text-4xl font-normal tracking-tight sm:text-5xl">Everything you need, <span class="text-zinc-500 dark:text-zinc-400">nothing you don't</span></h2>
                        <p class="mt-4 text-lg text-zinc-600 dark:text-zinc-400">The hard parts of a component library, handled, so you can build your app.</p>
                    </div>
                    <div class="mt-14 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($features as $feature)
                            <div class="group rounded-3xl bg-zinc-100 p-7 transition-colors hover:bg-zinc-200/70 dark:bg-zinc-900 dark:hover:bg-zinc-800/80">
                                <span class="flex size-11 items-center justify-center rounded-2xl bg-white text-gui-primary-text shadow-xs transition-transform group-hover:-rotate-6 group-hover:scale-110 dark:bg-black">
                                    <x-gui::icon :name="$feature['icon']" />
                                </span>
                                <h3 class="mt-6 text-lg">{{ $feature['title'] }}</h3>
                                <p class="mt-2 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ $feature['text'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- Theming: real controls restyling real components, scoped to the preview. --}}
            <section
                aria-labelledby="theming-heading"
                class="bg-zinc-100 px-4 py-24 sm:px-6 dark:bg-zinc-950"
                x-data="{
                    color: 'violet',
                    radius: 'lg',
                    dark: false,
                    radii: @js($radii),
                    // The preview starts in the page's mode; after that its switch is its own.
                    init() {
                        this.dark = document.documentElement.classList.contains('dark');
                    },
                    get css() {
                        return '@theme {\n    --color-gui-primary: var(--color-' + this.color + '-600);\n    --radius-gui: ' + this.radii[this.radius] + ';\n}';
                    },
                }"
            >
                <div class="mx-auto grid grid-cols-1 max-w-7xl gap-12 lg:grid-cols-[minmax(0,24rem)_minmax(0,1fr)]">
                    <div>
                        <h2 id="theming-heading" class="text-4xl font-normal tracking-tight sm:text-5xl">Make it <span class="text-zinc-500 dark:text-zinc-400">yours</span></h2>
                        <p class="mt-4 text-lg text-zinc-600 dark:text-zinc-400">Two variables restyle everything, in light and dark. Try it: the preview is live, with its own dark mode.</p>

                        <fieldset class="mt-10">
                            <legend class="text-sm font-medium">Primary colour</legend>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($colors as $color)
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="home_color" value="{{ $color }}" x-model="color" class="peer sr-only">
                                        <span class="sr-only">{{ ucfirst($color) }}</span>
                                        <span class="block size-9 rounded-full ring-2 ring-transparent ring-offset-2 ring-offset-zinc-100 transition peer-checked:ring-zinc-900 peer-focus-visible:outline-2 peer-focus-visible:outline-offset-4 peer-focus-visible:outline-zinc-900 dark:ring-offset-zinc-950 dark:peer-checked:ring-white dark:peer-focus-visible:outline-white" style="background: var(--color-{{ $color }}-600)" aria-hidden="true"></span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        <div class="mt-8 space-y-3">
                            <p id="home-radius-label" class="text-sm font-medium">Corners</p>
                            <x-gui::segmented-control name="home_radius" aria-labelledby="home-radius-label" size="sm" x-model="radius" :options="['none' => 'None', 'sm' => 'S', 'md' => 'M', 'lg' => 'L', 'xl' => 'XL', 'full' => 'Full']" value="lg" />
                        </div>

                        <div class="mt-8">
                            {{-- Only the preview: the page's own theme stays as it is (the button at the top changes that). --}}
                            <x-gui::switch name="home_dark" label="Dark preview" x-model="dark" />
                        </div>

                        <div class="mt-10">
                            <p class="mb-3 text-sm font-medium">Your theme</p>
                            @include('catalog::partials.code', ['model' => 'css'])
                        </div>
                    </div>

                    {{-- The preview: the theme's variables set on this box alone. --}}
                    <div
                        x-bind:class="dark ? 'dark' : 'light'"
                        x-bind:style="`--color-gui-primary: var(--color-${color}-600); --color-gui-primary-text: var(--color-${color}-${dark ? 400 : 600}); --radius-gui: ${radii[radius]}`"
                        class="self-start rounded-3xl bg-white p-6 text-zinc-900 shadow-xl ring-1 ring-zinc-200 transition-colors sm:p-8 dark:bg-zinc-900 dark:text-zinc-100 dark:ring-zinc-800"
                    >
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Acme Studio</p>
                                <p class="text-2xl font-semibold tracking-tight">Launch checklist</p>
                            </div>
                            <x-gui::badge color="primary" shape="pill" dot="primary">On track</x-gui::badge>
                        </div>

                        <div class="mt-6">
                            <x-gui::tabs variant="pills" :tabs="['tasks' => 'Tasks', 'files' => 'Files', 'activity' => 'Activity']">
                                <x-gui::tabs.panel name="tasks" class="space-y-4">
                                    <x-gui::checkbox name="home_task_1" label="Write the announcement" checked />
                                    <x-gui::checkbox name="home_task_2" label="Record the demo video" />
                                    <x-gui::checkbox name="home_task_3" label="Tell the newsletter" />
                                </x-gui::tabs.panel>
                                <x-gui::tabs.panel name="files">
                                    <x-gui::file-upload name="home_files" hint="Drop the press kit here." />
                                </x-gui::tabs.panel>
                                <x-gui::tabs.panel name="activity">
                                    <x-gui::timeline label="Activity">
                                        <x-gui::timeline.item title="Grace approved the copy" time="2h ago" icon="check" color="primary" />
                                        <x-gui::timeline.item title="Alan uploaded the logo" time="Yesterday" />
                                    </x-gui::timeline>
                                </x-gui::tabs.panel>
                            </x-gui::tabs>
                        </div>

                        <div class="mt-6 grid gap-5 sm:grid-cols-2">
                            <x-gui::field label="Launch budget" name="home_budget">
                                <x-gui::input type="number" prefix="$" value="2500" />
                            </x-gui::field>
                            <x-gui::field label="Confidence" name="home_confidence">
                                <x-gui::slider :value="80" suffix="%" />
                            </x-gui::field>
                        </div>

                        <div class="mt-6">
                            <x-gui::progress :value="64" label="Checklist" show-value />
                        </div>

                        <div class="mt-8 flex flex-wrap gap-2">
                            <x-gui::button x-on:click="GiraffeUI.toast('Launched!', { color: 'success' })">Launch</x-gui::button>
                            <x-gui::button variant="outline">Schedule</x-gui::button>
                            <x-gui::button variant="ghost">Preview</x-gui::button>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Blade in, accessible HTML out. --}}
            <section aria-labelledby="code-heading" class="px-4 py-24 sm:px-6">
                <div class="mx-auto grid grid-cols-1 max-w-7xl items-center gap-12 lg:grid-cols-2">
                    <div>
                        <h2 id="code-heading" class="text-4xl font-normal tracking-tight sm:text-5xl">Write Blade. <span class="text-zinc-500 dark:text-zinc-400">Ship accessible HTML.</span></h2>
                        <p class="mt-4 text-lg text-zinc-600 dark:text-zinc-400">This modal is a native dialog: focus is trapped, Escape closes it, the page behind goes inert, and focus comes back to the button. You write a few lines.</p>
                        <ul class="mt-8 space-y-3 text-sm">
                            @foreach (['Labels, hints and errors wired to fields for you', 'Old input and validation errors fill in by themselves', 'Translatable text, published with one command'] as $point)
                                <li class="flex gap-3"><x-gui::icon name="check-circle" size="sm" class="mt-0.5 text-gui-primary-text" />{{ $point }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="rounded-3xl bg-zinc-100 p-4 sm:p-6 dark:bg-zinc-900">
                        <x-gui::tabs variant="pills" :tabs="['preview' => 'Preview', 'code' => 'Blade']">
                            <x-gui::tabs.panel name="preview">
                                <div class="flex h-72 items-center justify-center rounded-2xl bg-white dark:bg-black">
                                    <x-gui::button color="ink" shape="pill" commandfor="home-invite" command="show-modal">Invite a teammate</x-gui::button>
                                </div>
                            </x-gui::tabs.panel>
                            <x-gui::tabs.panel name="code">
                                @include('catalog::partials.code', ['code' => $modalCode])
                            </x-gui::tabs.panel>
                        </x-gui::tabs>
                    </div>
                </div>
            </section>

            {{-- The examples, live and scaled down. --}}
            <section aria-labelledby="examples-heading" class="px-4 pb-24 sm:px-6">
                <div class="mx-auto max-w-7xl">
                    <div class="flex flex-wrap items-end justify-between gap-6">
                        <div class="max-w-2xl">
                            <h2 id="examples-heading" class="text-4xl font-normal tracking-tight sm:text-5xl">Whole apps, <span class="text-zinc-500 dark:text-zinc-400">not just parts</span></h2>
                            <p class="mt-4 text-lg text-zinc-600 dark:text-zinc-400">Full pages built only from GiraffeUI components. Open one and take it apart.</p>
                        </div>
                        <x-gui::button href="/catalog/examples/dashboard" variant="outline" color="ink" shape="pill">All examples <x-gui::icon name="arrow-right" size="sm" /></x-gui::button>
                    </div>
                    <div class="mt-12 grid gap-4 md:grid-cols-3">
                        @foreach ($examples as $example)
                            <a href="/catalog/examples/{{ $example['slug'] }}" class="group block rounded-3xl bg-zinc-100 p-3 transition hover:-translate-y-1 hover:shadow-xl focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-gui-primary-text dark:bg-zinc-900">
                                {{-- A live page at a third of its size; inert, so it's a picture you can't tab into. --}}
                                <div class="relative h-56 overflow-hidden rounded-2xl bg-white dark:bg-black" aria-hidden="true">
                                    <iframe src="/catalog/examples/{{ $example['slug'] }}" title="{{ $example['title'] }} example" loading="lazy" inert tabindex="-1" class="pointer-events-none absolute top-0 left-0 h-[300%] w-[300%] origin-top-left scale-[0.3334] border-0"></iframe>
                                </div>
                                <div class="flex items-center justify-between gap-3 px-3 pt-4 pb-2">
                                    <div>
                                        <p class="text-lg">{{ $example['title'] }}</p>
                                        <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $example['text'] }}</p>
                                    </div>
                                    <x-gui::icon name="arrow-up-right" class="text-zinc-500 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 dark:text-zinc-400" />
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- Livewire, welcome but optional. --}}
            <section aria-labelledby="livewire-heading" class="px-4 pb-24 sm:px-6">
                <div class="mx-auto grid grid-cols-1 max-w-7xl items-center gap-12 rounded-3xl bg-zinc-900 p-8 text-white sm:p-12 lg:grid-cols-2 dark:bg-zinc-900">
                    <div>
                        <x-gui::badge shape="pill" dot="var(--color-lime-400)" class="bg-white/10! text-zinc-200! ring-white/15!">Livewire</x-gui::badge>
                        <h2 id="livewire-heading" class="mt-6 text-4xl font-normal tracking-tight">Plays nicely with Livewire. <span class="text-zinc-400">Never needs it.</span></h2>
                        <p class="mt-4 text-zinc-300">wire:model binds every control, loading states show by themselves, and ids stay stable across renders. Leave Livewire out and everything still works with Alpine alone.</p>
                    </div>
                    @include('catalog::partials.code', ['code' => $livewireCode])
                </div>
            </section>

            {{-- The close. --}}
            <section class="px-3 pb-3">
                <div class="relative isolate mx-auto max-w-7xl overflow-hidden rounded-3xl bg-linear-to-br from-lime-300 via-violet-300 to-violet-600 px-6 py-20 text-center text-zinc-950">
                    <h2 class="text-4xl font-normal tracking-tight text-balance sm:text-6xl">Build your next Laravel app <span class="text-zinc-800">with GiraffeUI</span></h2>
                    <p class="mx-auto mt-5 max-w-xl text-lg text-zinc-800">Free and open source under the MIT licence. Install it in a minute.</p>
                    <div class="mx-auto mt-10 flex w-fit max-w-full items-center gap-3 rounded-full bg-white/70 py-1.5 ps-5 pe-1.5 font-mono text-sm backdrop-blur">
                        <code class="truncate">{{ $install }}</code>
                        <x-gui::copy-button :value="$install" label="Copy the install command" />
                    </div>
                    <div class="mt-8 flex flex-wrap justify-center gap-3">
                        <x-gui::button href="/catalog/installation" shape="pill" size="lg" class="bg-zinc-950! text-white! hover:bg-zinc-800!">Get started</x-gui::button>
                        <x-gui::button href="https://github.com/Jarryd-W-Hoffman/GiraffeUI" shape="pill" size="lg" class="bg-white/70! text-zinc-950! hover:bg-white!">Star on GitHub</x-gui::button>
                    </div>
                </div>
            </section>
        </main>

        <footer class="px-4 py-16 sm:px-6">
            <div class="mx-auto grid grid-cols-1 max-w-7xl gap-10 md:grid-cols-[minmax(0,1fr)_repeat(3,auto)] md:gap-16">
                <div>
                    <p class="flex items-center gap-2.5 text-lg font-semibold tracking-tight">
                        @include('catalog::partials.logo', ['class' => 'size-9'])
                        GiraffeUI
                    </p>
                    <p class="mt-3 max-w-xs text-sm text-zinc-600 dark:text-zinc-400">Accessible Blade and Alpine components for Laravel and Tailwind CSS v4.</p>
                </div>
                @foreach ($footer as $heading => $links)
                    <nav aria-label="{{ $heading }}">
                        <p class="text-sm font-medium">{{ $heading }}</p>
                        <ul class="mt-4 space-y-2.5 text-sm text-zinc-600 dark:text-zinc-400">
                            @foreach ($links as $text => $href)
                                <li><a href="{{ $href }}" class="hover:text-zinc-900 hover:underline dark:hover:text-zinc-100">{{ $text }}</a></li>
                            @endforeach
                        </ul>
                    </nav>
                @endforeach
            </div>
            <p class="mx-auto mt-12 max-w-7xl border-t border-zinc-200 pt-8 text-sm text-zinc-600 dark:border-zinc-800 dark:text-zinc-400">© {{ date('Y') }} GiraffeUI. MIT licensed. Icons by Heroicons.</p>
        </footer>

        <x-gui::modal id="home-invite" title="Invite a teammate" description="They'll get an email with a link to join." size="sm">
            <form id="home-invite-form" class="space-y-4" x-on:submit.prevent="$el.closest('dialog').close(); GiraffeUI.toast('Invitation sent', { color: 'success' })">
                <x-gui::field label="Email" name="home_invite_email" required>
                    <x-gui::input type="email" placeholder="grace@example.com" />
                </x-gui::field>
            </form>
            <x-slot:footer>
                <x-gui::modal.close>Cancel</x-gui::modal.close>
                <x-gui::button type="submit" form="home-invite-form" color="ink" shape="pill">Send invite</x-gui::button>
            </x-slot:footer>
        </x-gui::modal>

        {{-- ⌘K: every component, and the guides. --}}
        <x-gui::command-palette id="home-search" placeholder="Search components" label="Search components">
            <x-gui::command-palette.group heading="Guides">
                @foreach (\Workbench\App\Catalog::guides() as $slug => $guide)
                    <x-gui::command-palette.item :href="'/catalog/'.$slug" icon="document-text">{{ $guide }}</x-gui::command-palette.item>
                @endforeach
            </x-gui::command-palette.group>
            {{-- Not $component: Blade keeps that name for the component it's rendering. --}}
            @foreach (collect($components)->groupBy(fn ($entry) => $entry['group'] ?? 'Components', preserveKeys: true) as $group => $items)
                <x-gui::command-palette.group :heading="$group">
                    @foreach ($items as $slug => $entry)
                        <x-gui::command-palette.item :href="'/catalog/'.$slug" :keywords="$slug">{{ $entry['name'] }}</x-gui::command-palette.item>
                    @endforeach
                </x-gui::command-palette.group>
            @endforeach
        </x-gui::command-palette>

        <x-gui::toasts />
    </body>
</html>
