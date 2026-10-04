@props([
    'id',
    'label' => null,
    'position' => 'bottom-start',
])

@php
    $anchor = \JayAitch\GiraffeUI\Support\Anchor::name($id);
@endphp

{{-- The wrapper is the anchor; the panel is a native popover, so Escape and clicking outside close it. --}}
<span class="inline-flex" style="anchor-name: {{ $anchor }}">
    @isset($trigger)
        {{ $trigger }}
    @else
        <x-gui::button variant="outline" color="secondary" :commandfor="$id" command="toggle-popover">{{ $label }}</x-gui::button>
    @endisset
</span>

<div
    id="{{ $id }}"
    popover
    style="position-anchor: {{ $anchor }}"
    {{ $attributes->class([
        'inset-auto m-0 w-max max-w-[min(22rem,calc(100vw-1rem))] rounded-gui border border-zinc-200 bg-white p-4 text-sm text-zinc-900 shadow-lg dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-100',
        'opacity-0 transition-all transition-discrete duration-100 open:opacity-100 starting:open:opacity-0 motion-reduce:transition-none',
        \JayAitch\GiraffeUI\Support\Anchor::position($position),
    ]) }}
>
    {{ $slot }}
</div>
