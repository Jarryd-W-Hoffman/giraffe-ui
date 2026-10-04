@props([
    'lines' => null,
])

{{--
    A placeholder while content loads. Hidden from screen readers: mark the region that is loading
    with aria-busy="true" instead. Size it with classes, e.g. class="h-4 w-48".
--}}
@if ($lines)
    <div aria-hidden="true" {{ $attributes->class('space-y-2') }}>
        @for ($i = 1; $i <= $lines; $i++)
            <div @class([
                'h-4 animate-pulse rounded-gui bg-zinc-200 motion-reduce:animate-none dark:bg-zinc-800',
                'w-2/3' => $i === (int) $lines && $lines > 1,
            ])></div>
        @endfor
    </div>
@else
    <div aria-hidden="true" {{ $attributes->class('animate-pulse rounded-gui bg-zinc-200 motion-reduce:animate-none dark:bg-zinc-800') }}></div>
@endif
