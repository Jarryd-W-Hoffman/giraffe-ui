@props([
    'label',
    'value',
    'change' => null,
    'trend' => null,
    'invert' => false,
    'description' => null,
    'increaseLabel' => null,
    'decreaseLabel' => null,
    'variant' => 'default',
])

@php
    $increaseLabel ??= __('giraffeui::ui.increased_by');
    $decreaseLabel ??= __('giraffeui::ui.decreased_by');

    // Up is good unless invert is set (e.g. churn or response time). The arrow and hidden text carry the meaning, not just colour.
    $good = $trend === null ? null : (($trend === 'up') xor $invert);
@endphp

{{-- display: a big number with its label under it, centred, for a landing page's "in numbers" strip. --}}
@if ($variant === 'display')
<div {{ $attributes->class('text-center') }}>
    <p class="text-5xl font-normal tracking-tight tabular-nums">{{ $value }}</p>
    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ $label }}</p>
</div>
@else
<div {{ $attributes }}>
    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ $label }}</p>
    <p class="mt-1 text-2xl font-semibold tracking-tight text-zinc-900 tabular-nums dark:text-zinc-100">{{ $value }}</p>

    @if ($change !== null || $description)
        <p class="mt-1 flex flex-wrap items-center gap-x-1.5 text-sm text-zinc-600 dark:text-zinc-400">
            @if ($change !== null)
                <span @class([
                    'inline-flex items-center gap-0.5 font-medium tabular-nums',
                    'text-gui-success-text' => $good === true,
                    'text-gui-danger-text' => $good === false,
                    'text-zinc-900 dark:text-zinc-100' => $good === null,
                ])>
                    @if ($trend === 'up' || $trend === 'down')
                        <x-gui::icon :name="$trend === 'up' ? 'arrow-up' : 'arrow-down'" size="xs" />
                        <span class="sr-only">{{ $trend === 'up' ? $increaseLabel : $decreaseLabel }}</span>
                    @endif
                    {{ $change }}
                </span>
            @endif
            @if ($description)
                <span>{{ $description }}</span>
            @endif
        </p>
    @endif
</div>
@endif
