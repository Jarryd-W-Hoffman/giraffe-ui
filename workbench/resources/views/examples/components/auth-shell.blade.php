@props([
    'title',
])

{{--
    The page around the Sign in, Register and Forgot password examples, in the new look: the form on the page itself,
    and from lg a gradient panel beside it with a glimpse of the app (decoration, hidden from screen readers).
--}}
<div class="grid min-h-dvh bg-white lg:grid-cols-2 dark:bg-black">
    <main class="flex flex-col px-6 py-8 sm:px-12">
        <x-example::brand class="w-fit" />

        <div class="mx-auto my-auto w-full max-w-sm py-12">
            <h1 class="text-4xl font-normal tracking-tight">{{ $title }}</h1>
            @isset($subtitle)
                <p class="mt-3 text-zinc-600 dark:text-zinc-400">{{ $subtitle }}</p>
            @endisset

            <div class="mt-8">{{ $slot }}</div>

            @isset($footer)
                <p class="mt-8 text-xs text-zinc-600 dark:text-zinc-400">{{ $footer }}</p>
            @endisset
        </div>
    </main>

    <div class="hidden p-3 lg:block" aria-hidden="true">
        <div class="relative flex h-full flex-col justify-end overflow-hidden rounded-3xl bg-linear-to-br from-lime-300 via-violet-300 to-violet-600 p-10 dark:from-lime-400/80 dark:via-violet-500/70 dark:to-violet-700">
            {{-- A glimpse of the dashboard, floating on the gradient. --}}
            <div class="mb-auto ms-auto mt-6 w-72 rounded-3xl bg-black/85 p-6 text-white shadow-2xl backdrop-blur">
                <p class="text-sm text-zinc-400">Total revenue</p>
                <p class="mt-1 text-3xl font-semibold tracking-tight">$48,200<span class="text-zinc-500">.00</span></p>
                <div class="mt-5 flex h-16 items-end gap-1.5">
                    @foreach ([40, 55, 48, 70, 62, 85, 78, 95] as $height)
                        <span class="flex-1 rounded-full bg-violet-500" style="height: {{ $height }}%"></span>
                    @endforeach
                </div>
            </div>
            <p class="max-w-md text-4xl leading-tight font-normal tracking-tight text-zinc-950 dark:text-white">Every number your business runs on, in one calm place.</p>
            <p class="mt-4 text-zinc-800 dark:text-white/85">Revenue, customers and cash flow, as they happen.</p>
        </div>
    </div>
</div>
