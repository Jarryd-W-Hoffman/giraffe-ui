<!DOCTYPE html>
<html lang="en">
    <head>
        @include('catalog::partials.head')
        {{-- Alpine comes from Livewire, which injects its script (with Alpine bundled) on every page. --}}
    </head>
    <body class="bg-white text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
        @yield('content')

        {{-- Not part of the example: a way back to the catalog. --}}
        <a
            href="/catalog/introduction"
            class="fixed right-4 bottom-4 z-10 rounded-full bg-zinc-900 px-3 py-1.5 text-xs font-medium text-white shadow-lg hover:bg-zinc-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200"
        >← Back to the catalog</a>

        <x-gui::toasts />
    </body>
</html>
