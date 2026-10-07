@props([
    'href',
    'src' => null,
    'title',
    'description' => null,
])

{{--
    A link to a page, with the live page shown small above its name: an iframe at a third of its size. The frame is
    inert, so it's a picture you can't tab into, and it loads lazily. Use it for galleries of examples or templates.
--}}
<a href="{{ $href }}" {{ $attributes->class('group block rounded-3xl bg-zinc-100 p-3 transition hover:-translate-y-1 hover:shadow-xl focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-gui-primary-text motion-reduce:transition-none dark:bg-zinc-900') }}>
    <div class="relative h-56 overflow-hidden rounded-2xl bg-white dark:bg-black" aria-hidden="true">
        <iframe src="{{ $src ?? $href }}" title="{{ $title }}" loading="lazy" inert tabindex="-1" class="pointer-events-none absolute top-0 left-0 h-[300%] w-[300%] origin-top-left scale-[0.3334] border-0"></iframe>
    </div>
    <div class="flex items-center justify-between gap-3 px-3 pt-4 pb-2">
        <div>
            <p class="text-lg">{{ $title }}</p>
            @if ($description)
                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $description }}</p>
            @endif
        </div>
        <x-gui::icon name="arrow-up-right" class="text-zinc-500 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 dark:text-zinc-400" />
    </div>
</a>
