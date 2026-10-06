@extends('catalog::layout')

@section('toc')
    <li><a href="#customise" class="hover:text-zinc-900 dark:hover:text-zinc-100">Customise</a></li>
    <li><a href="#tokens" class="hover:text-zinc-900 dark:hover:text-zinc-100">Tokens</a></li>
    <li><a href="#dark-mode" class="hover:text-zinc-900 dark:hover:text-zinc-100">Dark mode</a></li>
@endsection

@section('content')
    @php
        $colours = ['indigo', 'violet', 'blue', 'sky', 'teal', 'emerald', 'rose', 'orange', 'zinc'];
        $radii = ['none', 'sm', 'md', 'lg', 'xl', '2xl'];
    @endphp

    <h1 class="text-3xl font-bold tracking-tight">Theming</h1>
    <p class="mt-3 text-lg text-zinc-600 dark:text-zinc-400">GiraffeUI is themed with Tailwind v4 CSS variables. Override them in your CSS. There's no PHP config.</p>

    <h2 id="customise" class="mt-12 scroll-mt-20 text-xl font-semibold tracking-tight">Customise</h2>
    <p class="mt-2 text-zinc-600 dark:text-zinc-400">Pick a primary colour and radius. Your choice carries across the whole catalog, so you can browse the components in your theme.</p>

    <div class="mt-4 overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-800" x-data="themer">
        <div class="space-y-5 border-b border-zinc-200 bg-zinc-50 p-5 dark:border-zinc-800 dark:bg-zinc-900/50">
            <fieldset>
                <legend class="mb-2 text-sm font-medium">Primary colour</legend>
                <div class="flex flex-wrap gap-2">
                    @foreach ($colours as $colour)
                        <label class="cursor-pointer" title="{{ $colour }}">
                            <input type="radio" name="primary" value="{{ $colour }}" x-model="primary" class="peer sr-only">
                            <span class="block size-8 rounded-full bg-{{ $colour }}-600 ring-offset-2 ring-offset-zinc-50 peer-checked:ring-2 peer-checked:ring-zinc-900 peer-focus-visible:ring-2 peer-focus-visible:ring-zinc-400 dark:ring-offset-zinc-900 dark:peer-checked:ring-zinc-100"></span>
                            <span class="sr-only">{{ $colour }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            <fieldset>
                <legend class="mb-2 text-sm font-medium">Radius</legend>
                <div class="flex flex-wrap gap-2">
                    @foreach ($radii as $radius)
                        <label class="cursor-pointer">
                            <input type="radio" name="radius" value="{{ $radius }}" x-model="radius" class="peer sr-only">
                            <span class="block rounded-md border border-zinc-300 bg-white px-3 py-1.5 text-sm peer-checked:border-zinc-900 peer-checked:font-medium peer-focus-visible:ring-2 peer-focus-visible:ring-zinc-400 dark:border-zinc-700 dark:bg-zinc-950 dark:peer-checked:border-zinc-100">{{ $radius }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            <x-gui::button variant="ghost" color="secondary" size="sm" x-on:click="reset()">Reset to default</x-gui::button>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-3 p-8">
            <x-gui::button>Primary</x-gui::button>
            <x-gui::button variant="outline">Outline</x-gui::button>
            <x-gui::button variant="ghost">Ghost</x-gui::button>
            <x-gui::button color="danger">Danger</x-gui::button>
        </div>

        <div class="border-t border-zinc-200 p-3 dark:border-zinc-800">
            @include('catalog::partials.code', ['model' => 'snippet'])
        </div>
    </div>

    <h2 id="tokens" class="mt-12 scroll-mt-20 text-xl font-semibold tracking-tight">Tokens</h2>
    <p class="mt-2 text-zinc-600 dark:text-zinc-400">Each colour has three tokens: the colour itself for solid backgrounds, <code class="font-mono text-sm">-foreground</code> for text on it, and <code class="font-mono text-sm">-text</code> for text, borders and focus rings on the page, which switches to a lighter shade in dark mode. Every default pairing meets WCAG AA in both modes. Override any of them in an <code class="font-mono text-sm">@@theme</code> block after the GiraffeUI import.</p>
    <div class="mt-4 overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-800">
        <table class="min-w-full text-start text-sm">
            <thead class="bg-zinc-50 text-zinc-500 dark:bg-zinc-900 dark:text-zinc-400">
                <tr>
                    <th scope="col" class="px-4 py-2.5 font-medium">Token</th>
                    <th scope="col" class="px-4 py-2.5 font-medium">Default</th>
                    <th scope="col" class="px-4 py-2.5 font-medium"><span class="sr-only">Swatch</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @foreach ([
                    ['--color-gui-primary', 'indigo-600', 'bg-gui-primary'],
                    ['--color-gui-secondary', 'zinc-600', 'bg-gui-secondary'],
                    ['--color-gui-success', 'emerald-700', 'bg-gui-success'],
                    ['--color-gui-warning', 'amber-400', 'bg-gui-warning'],
                    ['--color-gui-danger', 'red-600', 'bg-gui-danger'],
                    ['--color-gui-info', 'sky-700', 'bg-gui-info'],
                    ['--radius-gui', '--radius-md', null],
                ] as [$token, $default, $swatch])
                    <tr>
                        <td class="px-4 py-3 font-mono text-[13px]">{{ $token }}</td>
                        <td class="px-4 py-3 font-mono text-[13px] text-zinc-600 dark:text-zinc-400">{{ $default }}</td>
                        <td class="px-4 py-3">
                            @if ($swatch)
                                <span class="block size-5 rounded-full {{ $swatch }}"></span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <h2 id="dark-mode" class="mt-12 scroll-mt-20 text-xl font-semibold tracking-tight">Dark mode</h2>
    <p class="mt-2 text-zinc-600 dark:text-zinc-400">
        Components use Tailwind's <code class="font-mono text-sm">dark:</code> variant. The published layout switches it to a <code class="font-mono text-sm">.dark</code> class, set before the page paints, using the saved choice or the OS setting. Try it:
    </p>
    <div class="mt-4 flex flex-wrap gap-2">
        <x-gui::button variant="outline" color="secondary" size="sm" x-data x-on:click="GiraffeUI.setTheme('light')">Light</x-gui::button>
        <x-gui::button variant="outline" color="secondary" size="sm" x-data x-on:click="GiraffeUI.setTheme('dark')">Dark</x-gui::button>
        <x-gui::button variant="outline" color="secondary" size="sm" x-data x-on:click="GiraffeUI.setTheme('system')">System</x-gui::button>
    </div>
    <div class="mt-4">
        @include('catalog::partials.code', ['code' => "GiraffeUI.setTheme('dark'); // or 'light', or 'system'"])
    </div>
@endsection
