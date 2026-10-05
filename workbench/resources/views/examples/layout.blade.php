<!DOCTYPE html>
<html lang="en">
    <head>
        @include('catalog::partials.head')
        {{-- Alpine comes from Livewire, which injects its script (with Alpine bundled) on every page. --}}
    </head>
    {{-- x-data so Alpine directives anywhere on the page (x-on:click and so on) are live. --}}
    <body class="bg-white text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100" x-data>
        @yield('content')

        {{-- Not part of the example: a way back to the catalog. --}}
        <a
            href="/catalog/introduction"
            class="fixed right-4 bottom-4 z-10 rounded-full bg-zinc-900 px-3 py-1.5 text-xs font-medium text-white shadow-lg hover:bg-zinc-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200"
        >← Back to the catalog</a>

        {{-- Raised so toasts don't cover the link above (axe flags the overlapping targets). --}}
        <x-gui::toasts class="bottom-10!" />
    </body>
</html>
