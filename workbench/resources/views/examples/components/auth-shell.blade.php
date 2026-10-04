@props([
    'title',
])

{{-- The page around the Sign in, Register and Forgot password examples: brand, heading, a card and fine print. --}}
<main class="flex min-h-dvh items-center justify-center bg-zinc-50 px-4 py-12 dark:bg-zinc-950">
    <div class="w-full max-w-sm">
        <div class="mb-6 text-center">
            <span class="inline-flex size-10 items-center justify-center rounded-gui bg-gui-primary font-semibold text-gui-primary-foreground" aria-hidden="true">A</span>
            <h1 class="mt-4 text-xl font-semibold tracking-tight">{{ $title }}</h1>
            @isset($subtitle)
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                    {{ $subtitle }}
                </p>
            @endisset
        </div>

        <x-gui::card>
            {{ $slot }}
        </x-gui::card>

        @isset($footer)
            <p class="mt-6 text-center text-xs text-zinc-600 dark:text-zinc-400">
                {{ $footer }}
            </p>
        @endisset
    </div>
</main>
