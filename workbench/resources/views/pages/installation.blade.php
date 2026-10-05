@extends('catalog::layout')

@section('toc')
    <li><a href="#requirements" class="hover:text-zinc-900 dark:hover:text-zinc-100">Requirements</a></li>
    <li><a href="#install" class="hover:text-zinc-900 dark:hover:text-zinc-100">Install</a></li>
    <li><a href="#manual" class="hover:text-zinc-900 dark:hover:text-zinc-100">Manual setup</a></li>
    <li><a href="#alpine" class="hover:text-zinc-900 dark:hover:text-zinc-100">Alpine</a></li>
    <li><a href="#layout" class="hover:text-zinc-900 dark:hover:text-zinc-100">Layout</a></li>
    <li><a href="#browsers" class="hover:text-zinc-900 dark:hover:text-zinc-100">Browser support</a></li>
@endsection

@section('content')
    @php
        $manual = <<<'CSS'
            @import "tailwindcss";
            @import "../../vendor/jayaitch/giraffeui/resources/css/giraffeui.css";
            CSS;

        $alpine = <<<'JS'
            // resources/js/app.js, after npm install alpinejs
            import Alpine from 'alpinejs';

            window.Alpine = Alpine;
            Alpine.start();
            JS;

        $layout = <<<'BLADE'
            <x-layouts.app title="Dashboard">
                <x-gui::button>Hello</x-gui::button>
            </x-layouts.app>
            BLADE;
    @endphp

    <h1 class="text-3xl font-bold tracking-tight">Installation</h1>
    <p class="mt-3 text-lg text-zinc-600 dark:text-zinc-400">Add GiraffeUI to a Laravel app in two commands.</p>

    <h2 id="requirements" class="mt-12 scroll-mt-20 text-xl font-semibold tracking-tight">Requirements</h2>
    <ul class="mt-3 list-disc space-y-1 pl-5 text-zinc-600 dark:text-zinc-400">
        <li>PHP 8.4+</li>
        <li>Laravel 13</li>
        <li>Tailwind CSS v4 (new Laravel apps already have it)</li>
        <li><a href="#alpine" class="font-medium text-gui-primary-text hover:underline">Alpine</a> for the interactive parts. Livewire includes it, and Livewire itself is optional.</li>
    </ul>

    <h2 id="install" class="mt-12 scroll-mt-20 text-xl font-semibold tracking-tight">Install</h2>
    <div class="mt-4">
        @include('catalog::partials.code', ['code' => "composer require jayaitch/giraffeui\nphp artisan gui:install"])
    </div>
    <p class="mt-4 text-zinc-600 dark:text-zinc-400">The installer:</p>
    <ol class="mt-2 list-decimal space-y-1 pl-5 text-zinc-600 dark:text-zinc-400">
        <li>adds GiraffeUI's CSS to <code class="font-mono text-sm">resources/css/app.css</code>, after <code class="font-mono text-sm">@@import "tailwindcss"</code>;</li>
        <li>offers to install Livewire (optional, off by default);</li>
        <li>without Livewire, offers to start Alpine in <code class="font-mono text-sm">resources/js/app.js</code> and tells you to run <code class="font-mono text-sm">npm install alpinejs</code>;</li>
        <li>offers to publish an app layout with dark mode and toasts.</li>
    </ol>
    <p class="mt-3 text-zinc-600 dark:text-zinc-400">It is safe to run again, and never runs npm or edits Tailwind config files.</p>

    <h2 id="manual" class="mt-12 scroll-mt-20 text-xl font-semibold tracking-tight">Manual setup</h2>
    <p class="mt-3 text-zinc-600 dark:text-zinc-400">Prefer to do it yourself? Add one line to <code class="font-mono text-sm">resources/css/app.css</code>, then rebuild your CSS.</p>
    <div class="mt-4">
        @include('catalog::partials.code', ['code' => $manual])
    </div>

    <h2 id="alpine" class="mt-12 scroll-mt-20 text-xl font-semibold tracking-tight">Alpine</h2>
    <p class="mt-3 text-zinc-600 dark:text-zinc-400">
        Modals, drawers, dropdowns, popovers and accordions are native browser features and work without any JavaScript. The parts the browser has no built-in behaviour for (tabs, menu arrow keys, the combobox, the date picker's calendar, the number input's buttons, the slider's value, the textarea counter, file lists and toasts) use <a href="https://alpinejs.dev" class="font-medium text-gui-primary-text hover:underline">Alpine</a>.
    </p>
    <p class="mt-3 text-zinc-600 dark:text-zinc-400">
        With Livewire, you already have Alpine: Livewire bundles and starts it. Without Livewire, the installer can add it for you, or add it yourself:
    </p>
    <div class="mt-4">
        @include('catalog::partials.code', ['code' => $alpine])
    </div>
    <p class="mt-3 text-zinc-600 dark:text-zinc-400">
        Either way, GiraffeUI never calls Livewire itself. CI runs every component test and browser check twice, with Livewire and with Livewire removed, to keep it that way.
    </p>

    <h2 id="layout" class="mt-12 scroll-mt-20 text-xl font-semibold tracking-tight">Layout</h2>
    <p class="mt-3 text-zinc-600 dark:text-zinc-400">The published layout is an anonymous component. Wrap your pages in it:</p>
    <div class="mt-4">
        @include('catalog::partials.code', ['code' => $layout])
    </div>
    <p class="mt-3 text-zinc-600 dark:text-zinc-400">
        It sets the theme before the page paints (so dark mode never flashes), loads your CSS and JavaScript with Vite, and includes <code class="font-mono text-sm">&lt;x-gui::toasts /&gt;</code>, so <a href="/catalog/toasts" class="font-medium text-gui-primary-text hover:underline">toasts</a> work anywhere.
    </p>

    <h2 id="browsers" class="mt-12 scroll-mt-20 text-xl font-semibold tracking-tight">Browser support</h2>
    <p class="mt-3 text-zinc-600 dark:text-zinc-400">
        GiraffeUI targets current versions of Chrome, Edge, Firefox and Safari. It builds on recent platform features: <code class="font-mono text-sm">&lt;dialog&gt;</code>, the popover API, CSS anchor positioning (Baseline since January 2026) and invoker commands (<code class="font-mono text-sm">command</code>/<code class="font-mono text-sm">commandfor</code>). Older browsers without them can't open modals, menus or popovers.
    </p>
    <p class="mt-3 text-zinc-600 dark:text-zinc-400">
        Every change is tested in Chrome and Firefox. Safari uses the same engine features, but isn't part of the automated tests yet.
    </p>
@endsection
