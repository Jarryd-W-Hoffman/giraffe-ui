@props([
    'color' => 'primary',
    'variant' => 'solid',
    'icon' => null,
    'dismissible' => false,
    'remember' => null,
    'label' => null,
])

@php
    $label ??= __('giraffeui::ui.announcement');
    $key = $remember ? 'gui-banner:'.$remember : null;

    // Every class is written out in full so Tailwind can find it when scanning this file.
    $colors = [
        'solid' => [
            'primary' => 'bg-gui-primary text-gui-primary-foreground',
            'secondary' => 'bg-gui-secondary text-gui-secondary-foreground',
            'success' => 'bg-gui-success text-gui-success-foreground',
            'warning' => 'bg-gui-warning text-gui-warning-foreground',
            'danger' => 'bg-gui-danger text-gui-danger-foreground',
            'info' => 'bg-gui-info text-gui-info-foreground',
        ],
        'soft' => [
            'primary' => 'border-b border-gui-primary-text/20 bg-gui-primary/10 text-zinc-900 dark:text-zinc-100',
            'secondary' => 'border-b border-gui-secondary-text/20 bg-gui-secondary/10 text-zinc-900 dark:text-zinc-100',
            'success' => 'border-b border-gui-success-text/20 bg-gui-success/10 text-zinc-900 dark:text-zinc-100',
            'warning' => 'border-b border-gui-warning-text/20 bg-gui-warning/10 text-zinc-900 dark:text-zinc-100',
            'danger' => 'border-b border-gui-danger-text/20 bg-gui-danger/10 text-zinc-900 dark:text-zinc-100',
            'info' => 'border-b border-gui-info-text/20 bg-gui-info/10 text-zinc-900 dark:text-zinc-100',
        ],
    ];
    $style = ($colors[$variant] ?? $colors['solid'])[$color] ?? $colors['solid']['primary'];
@endphp

{{--
    A full-width notice for the top of the page. With dismissible it has a close button; with remember="some-key" a
    dismissed banner stays hidden on later visits (in this browser), hidden before the page paints so it never
    flashes. Dismissing fires a banner-dismissed event (detail: { key }), so an app can also record it on the server.
--}}
<section
    aria-label="{{ $label }}"
    @if ($dismissible)
        x-data
        x-on:click="if ($event.target.closest('[data-dismiss]')) {
            try { if (@js($key)) localStorage.setItem(@js($key), '1'); } catch {}
            $dispatch('banner-dismissed', { key: @js($remember) });
            $el.hidden = true;
        }"
    @endif
    {{ $attributes->class(['text-sm', $style]) }}
>
    <div class="mx-auto flex max-w-6xl items-center gap-3 px-4 py-2.5 sm:px-6">
        @if ($icon instanceof \Illuminate\View\ComponentSlot)
            <div class="shrink-0" aria-hidden="true">{{ $icon }}</div>
        @elseif ($icon)
            <x-gui::icon :name="$icon" size="sm" class="shrink-0" />
        @endif

        <div class="min-w-0 flex-1 sm:text-center">{{ $slot }}</div>

        @isset($action)
            <div class="shrink-0">{{ $action }}</div>
        @endisset

        @if ($dismissible)
            <button
                type="button"
                data-dismiss
                x-cloak
                aria-label="{{ __('giraffeui::ui.dismiss') }}"
                class="-me-1.5 flex size-8 shrink-0 cursor-pointer items-center justify-center rounded-gui opacity-80 transition-opacity hover:bg-black/10 hover:opacity-100 focus-visible:outline-2 focus-visible:outline-current dark:hover:bg-white/10"
            >
                <x-gui::icon name="x-mark" size="sm" />
            </button>
        @endif
    </div>
</section>
@if ($key)
    {{-- Runs as the page is parsed, before it paints, so a dismissed banner never flashes. --}}
    <script @if ($nonce = \Illuminate\Support\Facades\Vite::cspNonce()) nonce="{{ $nonce }}" @endif>try { if (localStorage.getItem(@js($key))) document.currentScript.previousElementSibling.hidden = true; } catch (e) {}</script>
@endif
