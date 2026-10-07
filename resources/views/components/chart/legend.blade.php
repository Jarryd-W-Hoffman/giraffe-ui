@props([
    'items' => [],
    'shape' => 'line',
])

{{--
    A chart's legend: always there for two or more series, so nobody has to match colours alone. The key mirrors the
    mark (a short line for lines, a square for bars and areas); the name stays in the normal text colour.
--}}
<ul {{ $attributes->class('flex flex-wrap gap-x-4 gap-y-1') }}>
    @foreach ($items as $item)
        <li class="flex items-center gap-1.5">
            @if ($shape === 'line' && ($item['dashed'] ?? false))
                <span class="w-3.5 shrink-0 border-t-2 border-dashed" style="border-color: {{ $item['color'] }}" aria-hidden="true"></span>
            @else
                <span @class(['shrink-0', 'h-0.5 w-3 rounded-full' => $shape === 'line', 'size-2.5 rounded-[2px]' => $shape !== 'line']) style="background: {{ $item['color'] }}" aria-hidden="true"></span>
            @endif
            {{ $item['name'] }}
        </li>
    @endforeach
</ul>
