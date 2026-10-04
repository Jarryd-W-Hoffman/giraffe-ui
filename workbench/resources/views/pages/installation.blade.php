@extends('catalog::layout')

@section('toc')
    <li><a href="#requirements" class="hover:text-zinc-900 dark:hover:text-zinc-100">Requirements</a></li>
    <li><a href="#install" class="hover:text-zinc-900 dark:hover:text-zinc-100">Install</a></li>
    <li><a href="#manual" class="hover:text-zinc-900 dark:hover:text-zinc-100">Manual setup</a></li>
    <li><a href="#layout" class="hover:text-zinc-900 dark:hover:text-zinc-100">Layout</a></li>
@endsection

@section('content')
    @php
        $manual = <<<'CSS'
            @import "tailwindcss";
            @import "../../vendor/jayaitch/giraffeui/resources/css/giraffeui.css";
            CSS;

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
        <li>PHP 8.3+</li>
        <li>Laravel 13</li>
        <li>Tailwind CSS v4 (new Laravel apps already have it)</li>
    </ul>

    <h2 id="install" class="mt-12 scroll-mt-20 text-xl font-semibold tracking-tight">Install</h2>
    <div class="mt-4">
        @include('catalog::partials.code', ['code' => "composer require jayaitch/giraffeui\nphp artisan gui:install"])
    </div>
    <p class="mt-4 text-zinc-600 dark:text-zinc-400">The installer:</p>
    <ol class="mt-2 list-decimal space-y-1 pl-5 text-zinc-600 dark:text-zinc-400">
        <li>adds GiraffeUI's CSS to <code class="font-mono text-sm">resources/css/app.css</code>, after <code class="font-mono text-sm">@@import "tailwindcss"</code>;</li>
        <li>offers to install Livewire (optional, off by default);</li>
        <li>offers to publish an app layout with dark mode.</li>
    </ol>
    <p class="mt-3 text-zinc-600 dark:text-zinc-400">It is safe to run again, and never runs npm or edits Tailwind config files.</p>

    <h2 id="manual" class="mt-12 scroll-mt-20 text-xl font-semibold tracking-tight">Manual setup</h2>
    <p class="mt-3 text-zinc-600 dark:text-zinc-400">Prefer to do it yourself? Add one line to <code class="font-mono text-sm">resources/css/app.css</code>, then rebuild your CSS.</p>
    <div class="mt-4">
        @include('catalog::partials.code', ['code' => $manual])
    </div>

    <h2 id="layout" class="mt-12 scroll-mt-20 text-xl font-semibold tracking-tight">Layout</h2>
    <p class="mt-3 text-zinc-600 dark:text-zinc-400">The published layout is an anonymous component. Wrap your pages in it:</p>
    <div class="mt-4">
        @include('catalog::partials.code', ['code' => $layout])
    </div>
@endsection
