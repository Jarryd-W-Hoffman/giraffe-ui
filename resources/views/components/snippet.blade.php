@props([
    'value' => null,
    'prompt' => '$',
    'copyLabel' => null,
])

@php
    $value ??= trim($slot->toHtml());
    $copyLabel ??= __('giraffeui::ui.copy');
@endphp

{{--
    One line of code to copy, such as an install command: a prompt, the code, and a copy button. The prompt is
    decoration and isn't copied. Set :prompt="false" to leave it out.
--}}
<div {{ $attributes->class('flex w-fit max-w-full items-center gap-3 rounded-full bg-zinc-100 py-1.5 ps-5 pe-1.5 font-mono text-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-zinc-800') }}>
    @if ($prompt)
        <span class="text-zinc-400 select-none dark:text-zinc-500" aria-hidden="true">{{ $prompt }}</span>
    @endif
    <code class="truncate">{{ $value }}</code>
    <x-gui::copy-button :value="$value" :label="$copyLabel" class="[&_button]:rounded-full" />
</div>
