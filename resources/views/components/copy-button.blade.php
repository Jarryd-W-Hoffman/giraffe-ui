@props([
    'value' => null,
    'target' => null,
    'label' => null,
    'copiedLabel' => null,
    'variant' => 'outline',
    'color' => 'secondary',
    'size' => 'sm',
])

@php
    $label ??= __('giraffeui::ui.copy');
    $copiedLabel ??= __('giraffeui::ui.copied');
    $iconOnly = $slot->isEmpty();

    // Icon-only buttons are square and quiet, so they sit inside inputs and code blocks.
    $squares = ['xs' => 'size-7', 'sm' => 'size-8', 'md' => 'size-10'];
@endphp

{{--
    Copies text to the clipboard: value, or the text of target (a CSS selector: an input's value, or an element's
    text). It confirms with a check and "Copied" for two seconds, and says so to screen readers. Hidden until Alpine
    starts, since it can't work without JavaScript. Dispatches a "copied" event with the text.
--}}
<span
    x-data="{
        state: null,
        timer: null,
        async copy() {
            let text = this.$root.dataset.copy;
            if (text === undefined) {
                const source = document.querySelector(this.$root.dataset.copyTarget);
                text = source ? ('value' in source && source.matches('input, textarea, select') ? source.value : source.innerText.trim()) : '';
            }
            let done = false;
            try {
                await navigator.clipboard.writeText(text);
                done = true;
            } catch (e) {
                // The Clipboard API needs a secure context (HTTPS or localhost): fall back to a hidden textarea.
                const area = Object.assign(document.createElement('textarea'), { value: text, readOnly: true });
                area.style.cssText = 'position: fixed; opacity: 0';
                document.body.append(area);
                area.select();
                try { done = document.execCommand('copy'); } catch (e) {}
                area.remove();
                this.$refs.button.focus();
            }
            this.state = done ? 'copied' : 'failed';
            if (done) this.$dispatch('copied', { text });
            clearTimeout(this.timer);
            this.timer = setTimeout(() => this.state = null, 2000);
        },
    }"
    @if ($value !== null) data-copy="{{ $value }}" @else data-copy-target="{{ $target }}" @endif
    x-cloak
    {{ $attributes->only('class')->class('inline-flex') }}
>
    @if ($iconOnly)
        <button
            type="button"
            x-ref="button"
            aria-label="{{ $label }}"
            x-on:click="copy()"
            {{ $attributes->except('class')->class(['flex shrink-0 cursor-pointer items-center justify-center rounded-[calc(var(--radius-gui)-2px)] text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-gui-primary-text dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100', $squares[$size] ?? $squares['sm']]) }}
        >
            <x-gui::icon name="document-duplicate" size="sm" x-show="state !== 'copied'" />
            <x-gui::icon name="check" size="sm" class="text-gui-success-text" x-show="state === 'copied'" x-cloak />
        </button>
    @else
        <x-gui::button type="button" :variant="$variant" :color="$color" :size="$size === 'xs' ? 'sm' : $size" x-ref="button" x-on:click="copy()" {{ $attributes->except('class') }}>
            <x-gui::icon name="document-duplicate" size="sm" x-show="state !== 'copied'" />
            <x-gui::icon name="check" size="sm" x-show="state === 'copied'" x-cloak />
            <span x-show="state !== 'copied'">{{ $slot }}</span>
            <span x-show="state === 'copied'" x-cloak>{{ $copiedLabel }}</span>
        </x-gui::button>
    @endif
    {{-- Said once, after a copy (or a failed one). --}}
    <span role="status" class="sr-only" x-text="state === 'copied' ? @js($copiedLabel) : state === 'failed' ? @js(__('giraffeui::ui.copy_failed')) : ''"></span>
</span>
