{{-- A code block with a copy button. Pass $code, or $model for Alpine-driven code. --}}
<div class="group relative" x-data="{ copied: false }">
    <pre tabindex="0" class="overflow-x-auto focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text rounded-xl border border-zinc-800 bg-zinc-950 p-4 pe-20 text-[13px] leading-relaxed text-zinc-100 dark:bg-zinc-900"><code x-ref="code" @isset($model) x-text="{{ $model }}" @endisset>{{ $code ?? '' }}</code></pre>
    <button
        type="button"
        class="absolute top-2.5 end-2.5 rounded-md border border-zinc-700 bg-zinc-900 px-2 py-1 text-xs text-zinc-300 opacity-70 transition hover:text-white hover:opacity-100 focus-visible:opacity-100 group-hover:opacity-100"
        x-on:click="navigator.clipboard.writeText($refs.code.textContent); copied = true; setTimeout(() => copied = false, 1500)"
        x-text="copied ? 'Copied' : 'Copy'"
    >Copy</button>
</div>
