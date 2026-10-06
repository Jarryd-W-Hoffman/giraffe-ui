@use('JayAitch\GiraffeUI\Support\Chart')

@props([
    'data' => [],
    'color' => 'chart-1',
    'sort' => true,
    'limit' => null,
    'percent' => false,
    'prefix' => '',
    'suffix' => '',
    'compact' => false,
    'decimals' => null,
    'label' => null,
])

@php
    // name => value, or name => ['value' => …, 'href' => …]. Largest first unless sort is off; limit keeps the top few.
    $rows = collect($data)
        ->map(fn ($row, $name) => is_array($row) ? ['name' => (string) $name, 'value' => $row['value'] ?? null, 'href' => $row['href'] ?? null] : ['name' => (string) $name, 'value' => $row, 'href' => null])
        ->filter(fn ($row) => is_numeric($row['value']))
        ->values();
    if ($sort) {
        $rows = $rows->sortByDesc('value')->values();
    }
    $total = $rows->sum('value');
    if ($limit) {
        $rows = $rows->take((int) $limit);
    }
    $max = max(1e-9, (float) $rows->max('value'));
    $format = fn ($value) => Chart::format($value, $prefix, $suffix, $decimals, $compact);
@endphp

{{--
    A ranked list with a bar behind each name, as long as its share of the largest: top pages, referrers, countries.
    It's a real list, so screen readers read each name and its value; the bars are decoration. Rows can link.
--}}
<div {{ $attributes->class('min-w-0 text-sm') }}>
    @if ($rows->isEmpty())
        <p class="rounded-gui border border-dashed border-zinc-200 py-6 text-center text-zinc-600 dark:border-zinc-800 dark:text-zinc-400">{{ __('giraffeui::ui.no_data') }}</p>
    @else
        <ul @if ($label) aria-label="{{ $label }}" @endif class="space-y-1">
            @foreach ($rows as $row)
                @php($share = $row['value'] / $max * 100)
                <li class="flex items-center gap-3">
                    <div class="relative min-w-0 flex-1">
                        {{-- The bar: the series colour, softened so the name on top keeps its contrast. --}}
                        <span class="absolute inset-y-0 start-0 rounded-[calc(var(--radius-gui)-2px)]" style="width: max({{ round($share, 2) }}%, 2px); background: color-mix(in oklab, var(--color-gui-{{ $color }}) 22%, transparent)" aria-hidden="true"></span>
                        @if ($row['href'])
                            <a href="{{ $row['href'] }}" class="relative block truncate rounded-[calc(var(--radius-gui)-2px)] px-2 py-1.5 text-zinc-900 hover:underline focus-visible:outline-2 focus-visible:outline-gui-primary-text dark:text-zinc-100">{{ $row['name'] }}</a>
                        @else
                            <span class="relative block truncate px-2 py-1.5 text-zinc-900 dark:text-zinc-100">{{ $row['name'] }}</span>
                        @endif
                    </div>
                    <span class="shrink-0 text-end font-medium text-zinc-900 tabular-nums dark:text-zinc-100">{{ $format($row['value']) }}</span>
                    @if ($percent && $total > 0)
                        <span class="w-10 shrink-0 text-end text-zinc-600 tabular-nums dark:text-zinc-400">{{ ($share = $row['value'] / $total * 100) < 1 && $share > 0 ? '<1' : round($share) }}%</span>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
</div>
