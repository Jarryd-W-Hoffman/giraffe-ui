@props([
    'data' => [],
    'color' => null,
    'area' => false,
    'label' => null,
    'prefix' => '',
    'suffix' => '',
    'compact' => false,
])

@php
    $values = array_values(collect($data)->all());
    $numbers = array_values(array_filter($values, fn ($value) => is_numeric($value)));
    $format = fn ($value) => \JayAitch\GiraffeUI\Support\Chart::format($value, $prefix, $suffix, null, $compact);

    if ($numbers !== []) {
        $low = min($numbers);
        $high = max($numbers);
        // A flat line sits in the middle rather than on an edge.
        [$min, $max] = $low == $high ? [$low - 1, $high + 1] : [$low, $high];
        $count = count($values);
        $lastIndex = array_key_last(array_filter($values, fn ($value) => is_numeric($value)));
        $label ??= __('giraffeui::ui.trend', [
            'first' => $format($numbers[0]),
            'last' => $format(end($numbers)),
            'high' => $format($high),
            'low' => $format($low),
        ]);
    }

    // h-8 w-24 unless the classes size it, since a component's own classes would otherwise compete with yours.
    $classes = ' '.$attributes->get('class', '');
    $size = trim((preg_match('/\s(h|size)-/', $classes) ? '' : 'h-8 ').(preg_match('/\s(w|size)-/', $classes) ? '' : 'w-24'));

    // The line is the quiet, de-emphasis grey and the latest point is the accent, unless a colour is chosen for both.
    $accent = 'var(--color-gui-'.($color ?? 'chart-1').')';
    $stroke = $color ? $accent : null;
@endphp

{{--
    A small trend line for a stat or a table row: no axes, sized by its classes (h-8 w-24 by default).
    The latest point is marked, and screen readers hear the trend's start, end, high and low.
--}}
@if ($numbers === [])
    <span {{ $attributes->class(['inline-block', $size]) }}></span>
@else
    <span role="img" aria-label="{{ $label }}" {{ $attributes->class(['relative inline-block align-middle', $size]) }}>
        {{-- Inset by the dot's radius, so the dot and the line's ends never get clipped. --}}
        <span class="absolute inset-1">
            <svg viewBox="0 0 {{ \JayAitch\GiraffeUI\Support\Chart::SIZE }} {{ \JayAitch\GiraffeUI\Support\Chart::SIZE }}" preserveAspectRatio="none" aria-hidden="true" @class(['absolute inset-0 size-full overflow-visible', 'text-zinc-400 dark:text-zinc-500' => ! $stroke]) @if ($stroke) style="color: {{ $stroke }}" @endif>
                @if ($area)
                    <path d="{{ \JayAitch\GiraffeUI\Support\Chart::area($values, $min, $max) }}" fill="currentColor" fill-opacity="0.12" />
                @endif
                <path d="{{ \JayAitch\GiraffeUI\Support\Chart::line($values, $min, $max) }}" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" stroke-linecap="round" vector-effect="non-scaling-stroke" />
            </svg>
            <span
                class="absolute size-2 -translate-x-1/2 -translate-y-1/2 rounded-full ring-2 ring-(--gui-chart-surface)"
                style="left: {{ \JayAitch\GiraffeUI\Support\Chart::x($lastIndex, $count) / 10 }}%; top: {{ \JayAitch\GiraffeUI\Support\Chart::y($values[$lastIndex], $min, $max) / 10 }}%; background: {{ $accent }}"
            ></span>
        </span>
    </span>
@endif
