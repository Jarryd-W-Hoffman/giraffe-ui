@props([
    'variant' => 'segmented',
    'labels' => true,
    'size' => 'md',
    'label' => null,
])

@php
    $label ??= __($variant === 'toggle' ? 'giraffeui::ui.dark_mode' : 'giraffeui::ui.theme');
    $name = \JayAitch\GiraffeUI\Support\Id::make('gui-theme');
    $themes = [
        'light' => ['sun', __('giraffeui::ui.light')],
        'dark' => ['moon', __('giraffeui::ui.dark')],
        'system' => ['computer-desktop', __('giraffeui::ui.system')],
    ];

    // Every class is written out in full so Tailwind can find it when scanning this file.
    $sizes = [
        'sm' => $labels ? 'h-7 gap-1.5 px-2.5 text-xs' : 'size-7',
        'md' => $labels ? 'h-8 gap-1.5 px-3 text-sm' : 'size-8',
        'lg' => $labels ? 'h-10 gap-2 px-4 text-sm' : 'size-10',
    ];
    $toggleSizes = ['sm' => 'size-7', 'md' => 'size-9', 'lg' => 'size-10'];
@endphp

{{--
    Light, dark or the system's setting, saved in this browser. It uses GiraffeUI.setTheme() from the published
    layout (and defines it if your layout doesn't), follows the system as it changes, and every switcher on the page
    stays in sync with the theme, however it was changed. The layout's head script applies the saved theme before
    the page paints.
--}}
<div
    x-data="{
        theme: 'system',
        dark: false,
        init() {
            const gui = window.GiraffeUI ??= {};
            gui.applyTheme ??= () => {
                let saved = null;
                try { saved = localStorage.getItem('theme'); } catch (e) {}
                const dark = saved ? saved === 'dark' : matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.classList.toggle('dark', dark);
            };
            gui.setTheme ??= (theme) => {
                try { theme === 'system' ? localStorage.removeItem('theme') : localStorage.setItem('theme', theme); } catch (e) {}
                gui.applyTheme();
            };
            // Once per page: with the system theme, follow the OS as it switches.
            if (! gui.watchingSystemTheme) {
                gui.watchingSystemTheme = true;
                matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => gui.applyTheme());
            }
            this.sync();
            new MutationObserver(() => this.sync()).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
        },
        // Read the saved choice and the theme in use, wherever they were changed.
        sync() {
            let saved = null;
            try { saved = localStorage.getItem('theme'); } catch (e) {}
            this.theme = saved === 'light' || saved === 'dark' ? saved : 'system';
            this.dark = document.documentElement.classList.contains('dark');
        },
        set(theme) {
            window.GiraffeUI.setTheme(theme);
            this.sync();
        },
    }"
    {{ $attributes->only('class')->class('inline-flex max-w-full') }}
>
    @if ($variant === 'toggle')
        <button
            type="button"
            aria-label="{{ $label }}"
            aria-pressed="false"
            x-bind:aria-pressed="dark.toString()"
            x-on:click="set(dark ? 'light' : 'dark')"
            {{ $attributes->except('class')->class(['flex cursor-pointer items-center justify-center rounded-gui text-zinc-600 transition-colors hover:bg-zinc-100 hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-gui-primary-text dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100', $toggleSizes[$size] ?? $toggleSizes['md']]) }}
        >
            {{-- The icon shows what you'd switch to. --}}
            <x-gui::icon name="moon" size="sm" class="dark:hidden" />
            <x-gui::icon name="sun" size="sm" class="hidden dark:block" />
        </button>
    @else
        {{-- Native radios, like the segmented control: one tab stop and arrow keys between the three. --}}
        <div
            role="radiogroup"
            aria-label="{{ $label }}"
            {{ $attributes->except('class')->class('inline-flex max-w-full gap-1 rounded-gui bg-zinc-100 p-1 dark:bg-zinc-800') }}
        >
            @foreach ($themes as $value => [$icon, $text])
                <label @class([
                    'relative flex min-w-0 cursor-pointer items-center justify-center rounded-[calc(var(--radius-gui)-2px)] font-medium whitespace-nowrap text-zinc-600 transition-colors select-none',
                    'hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100',
                    'has-checked:bg-white has-checked:text-zinc-900 has-checked:shadow-xs dark:has-checked:bg-zinc-950 dark:has-checked:text-zinc-100',
                    'has-focus-visible:outline-2 has-focus-visible:outline-offset-1 has-focus-visible:outline-gui-primary-text',
                    $sizes[$size] ?? $sizes['md'],
                ])>
                    <input
                        type="radio"
                        name="{{ $name }}"
                        value="{{ $value }}"
                        class="sr-only"
                        @if ($value === 'system') checked @endif
                        x-bind:checked="theme === @js($value)"
                        x-on:change="set(@js($value))"
                        @unless ($labels) aria-label="{{ $text }}" @endunless
                    >
                    <x-gui::icon :name="$icon" size="sm" />
                    @if ($labels)
                        <span>{{ $text }}</span>
                    @endif
                </label>
            @endforeach
        </div>
    @endif
</div>
