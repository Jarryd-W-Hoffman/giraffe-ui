@props([
    'label',
    'value',
    'change' => null,
    'trend' => null,
    'invert' => false,
    'description' => null,
    'increaseLabel' => 'Increased by',
    'decreaseLabel' => 'Decreased by',
])

@php
    // Up is good unless invert is set (e.g. churn or response time). The arrow and hidden text carry the meaning, not just colour.
    $good = $trend === null ? null : (($trend === 'up') xor $invert);
@endphp

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
