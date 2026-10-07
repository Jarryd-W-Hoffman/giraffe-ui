@props([
    'links' => [],
    'description' => null,
    'container' => 'max-w-6xl',
    'variant' => 'columns',
    'newsletter' => null,
    'newsletterAction' => '#',
    'newsletterButton' => null,
])

@php
    $variant = in_array($variant, ['columns', 'simple', 'centered', 'newsletter'], true) ? $variant : 'columns';
    $newsletter ??= __('giraffeui::ui.newsletter');
    $newsletterButton ??= __('giraffeui::ui.subscribe');
    // simple and centered show every link in one row.
    $flat = collect($links)->flatMap(fn ($group) => $group)->all();
    $link = 'rounded-sm hover:text-zinc-900 hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text dark:hover:text-zinc-100';
@endphp

{{--
    A site footer.
      columns     brand and a line about it, then columns of links (the default)
      simple      one row: brand, links, fine print
      centered    everything centred, with room for social links
      newsletter  columns, with an email sign-up beside them
    links is heading => [text => href]; each column is a nav named by its heading. The legal slot is the fine print;
    social holds icon links (give each an aria-label).
--}}
<footer {{ $attributes->class(['px-4 sm:px-6', $variant === 'simple' ? 'py-8' : 'py-16']) }}>
    @if ($variant === 'simple')
        <div class="mx-auto flex flex-col items-center justify-between gap-6 text-sm md:flex-row {{ $container }}">
            @isset($brand)
                <p class="flex items-center gap-2.5 font-semibold tracking-tight">{{ $brand }}</p>
            @endisset
            <nav aria-label="{{ __('giraffeui::ui.footer') }}">
                <ul class="flex flex-wrap justify-center gap-x-6 gap-y-2 text-zinc-600 dark:text-zinc-400">
                    @foreach ($flat as $text => $href)
                        <li><a href="{{ $href }}" class="{{ $link }}">{{ $text }}</a></li>
                    @endforeach
                </ul>
            </nav>
            @isset($legal)
                <p class="text-zinc-600 dark:text-zinc-400">{{ $legal }}</p>
            @endisset
        </div>
    @elseif ($variant === 'centered')
        <div class="mx-auto flex flex-col items-center text-center {{ $container }}">
            @isset($brand)
                <p class="flex items-center gap-2.5 text-lg font-semibold tracking-tight">{{ $brand }}</p>
            @endisset
            @if ($description)
                <p class="mt-3 max-w-sm text-sm text-zinc-600 dark:text-zinc-400">{{ $description }}</p>
            @endif
            <nav aria-label="{{ __('giraffeui::ui.footer') }}" class="mt-8">
                <ul class="flex flex-wrap justify-center gap-x-8 gap-y-3 text-sm text-zinc-600 dark:text-zinc-400">
                    @foreach ($flat as $text => $href)
                        <li><a href="{{ $href }}" class="{{ $link }}">{{ $text }}</a></li>
                    @endforeach
                </ul>
            </nav>
            @isset($social)
                <div class="mt-8 flex items-center gap-2">{{ $social }}</div>
            @endisset
            @isset($legal)
                <p class="mt-10 w-full border-t border-zinc-200 pt-8 text-sm text-zinc-600 dark:border-zinc-800 dark:text-zinc-400">{{ $legal }}</p>
            @endisset
        </div>
    @else
        <div @class([
            'mx-auto grid grid-cols-1 gap-10 md:gap-16',
            'md:grid-cols-[minmax(0,1fr)_repeat(3,auto)]' => $variant === 'columns',
            'md:grid-cols-[minmax(0,1fr)_repeat(3,auto)] xl:grid-cols-[minmax(0,1fr)_repeat(3,auto)_minmax(0,20rem)]' => $variant === 'newsletter',
            $container,
        ])>
            <div>
                @isset($brand)
                    <p class="flex items-center gap-2.5 text-lg font-semibold tracking-tight">{{ $brand }}</p>
                @endisset
                @if ($description)
                    <p class="mt-3 max-w-xs text-sm text-zinc-600 dark:text-zinc-400">{{ $description }}</p>
                @endif
                @isset($social)
                    <div class="mt-6 flex items-center gap-2">{{ $social }}</div>
                @endisset
            </div>
            @foreach ($links as $heading => $group)
                <nav aria-label="{{ $heading }}">
                    <p class="text-sm font-medium">{{ $heading }}</p>
                    <ul class="mt-4 space-y-2.5 text-sm text-zinc-600 dark:text-zinc-400">
                        @foreach ($group as $text => $href)
                            <li><a href="{{ $href }}" class="{{ $link }}">{{ $text }}</a></li>
                        @endforeach
                    </ul>
                </nav>
            @endforeach
            @if ($variant === 'newsletter')
                {{-- A real form: it posts the email to newsletter-action. --}}
                <form method="POST" action="{{ $newsletterAction }}" class="max-w-md md:col-span-full xl:col-span-1">
                    @csrf
                    <p class="text-sm font-medium">{{ $newsletter }}</p>
                    @isset($newsletterText)
                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ $newsletterText }}</p>
                    @endisset
                    <div class="mt-4 flex gap-2">
                        <x-gui::input type="email" name="email" required autocomplete="email" :aria-label="$newsletter" placeholder="you@example.com" size="sm" class="min-w-0 flex-1" />
                        <x-gui::button type="submit" color="ink" shape="pill" size="sm">{{ $newsletterButton }}</x-gui::button>
                    </div>
                </form>
            @endif
        </div>
        @isset($legal)
            <p class="mx-auto mt-12 border-t border-zinc-200 pt-8 text-sm text-zinc-600 dark:border-zinc-800 dark:text-zinc-400 {{ $container }}">{{ $legal }}</p>
        @endisset
    @endif
</footer>
