<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        <!--
            Dark mode: runs before the page paints so there's no flash of the wrong theme.
            Saved choice wins, otherwise follow the OS. Switch with GiraffeUI.setTheme('dark' | 'light' | 'system').
        -->
        <script>
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
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-white text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
        {{ $slot }}

        {{-- Shows GiraffeUI.toast(...), $dispatch('toast', ...) and session()->flash('toast', ...). --}}
        <x-gui::toasts />
    </body>
</html>
