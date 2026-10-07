@props([
    'code' => null,
    'live' => null,
    'copyLabel' => null,
    'copiedLabel' => null,
])

@php
    $copyLabel ??= __('giraffeui::ui.copy');
    $copiedLabel ??= __('giraffeui::ui.copied');
@endphp

{{--
    A block of code with a Copy button. Pass the code as code or in the slot, or live, an Alpine expression whose value
    is shown (and copied) as it changes. It scrolls sideways rather than wrapping, and can take keyboard focus to do so.
--}}
<div {{ $attributes->class('group relative') }} x-data="{
    copied: false,
    async copy() {
        const text = this.$refs.code.textContent;
        try {
            await navigator.clipboard.writeText(text);
        } catch (e) {
            const area = Object.assign(document.createElement('textarea'), { value: text, readOnly: true });
            area.style.cssText = 'position: fixed; opacity: 0';
            document.body.append(area);
            area.select();
            try { document.execCommand('copy'); } catch (e) {}
            area.remove();
        }
        this.copied = true;
        setTimeout(() => this.copied = false, 1500);
    },
}">
    <pre tabindex="0" class="overflow-x-auto rounded-xl border border-zinc-800 bg-zinc-950 p-4 pe-20 text-[13px] leading-relaxed text-zinc-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text dark:bg-zinc-900"><code x-ref="code" @if ($live) x-text="{{ $live }}" @endif>{{ $code ?? $slot }}</code></pre>
    <button
        type="button"
        class="absolute end-2.5 top-2.5 cursor-pointer rounded-md border border-zinc-700 bg-zinc-900 px-2 py-1 text-xs text-zinc-300 opacity-70 transition group-hover:opacity-100 hover:text-white hover:opacity-100 focus-visible:opacity-100"
        x-on:click="copy()"
        x-text="copied ? @js($copiedLabel) : @js($copyLabel)"
    >{{ $copyLabel }}</button>
    <span role="status" class="sr-only" x-text="copied ? @js($copiedLabel) : ''"></span>
</div>
