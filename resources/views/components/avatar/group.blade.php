@props([
    'avatars' => null,
    'max' => null,
    'more' => 0,
    'size' => 'md',
    'label' => null,
    'moreLabel' => ':count more',
])

@php
    // From an array, show up to max and count the rest. In the slot, pass more yourself.
    $avatars = $avatars === null ? null : collect($avatars)->values();
    $shown = $avatars && $max ? $avatars->take((int) $max) : $avatars;
    $hidden = (int) $more + ($avatars ? $avatars->count() - $shown->count() : 0);

    $overlaps = ['sm' => '-space-x-1', 'md' => '-space-x-2', 'lg' => '-space-x-2.5'];

    $sizes = [
        'sm' => 'size-8 text-xs',
        'md' => 'size-10 text-sm',
        'lg' => 'size-12 text-base',
    ];
@endphp

{{-- Each avatar has a ring in the page colour so overlaps read clearly. Change it with e.g. class="*:ring-zinc-50!". --}}
<div
    @if ($label) role="group" aria-label="{{ $label }}" @endif
    {{ $attributes->class([
        'flex items-center *:ring-2 *:ring-white dark:*:ring-zinc-950',
        $overlaps[$size] ?? $overlaps['md'],
    ]) }}
>
    @if ($shown)
        @foreach ($shown as $avatar)
            <x-gui::avatar :src="$avatar['src'] ?? null" :name="$avatar['name'] ?? null" :alt="$avatar['alt'] ?? null" :size="$size" />
        @endforeach
    @else
        {{ $slot }}
    @endif

    @if ($hidden > 0)
        <span role="img" aria-label="{{ str_replace(':count', $hidden, $moreLabel) }}" @class(['inline-flex shrink-0 items-center justify-center rounded-full bg-zinc-100 font-medium text-zinc-700 tabular-nums dark:bg-zinc-800 dark:text-zinc-200', $sizes[$size] ?? $sizes['md']])>
            <span aria-hidden="true">+{{ $hidden }}</span>
        </span>
    @endif
</div>
