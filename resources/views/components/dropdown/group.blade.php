@props([
    'heading' => null,
])

@php
    $id = \JayAitch\GiraffeUI\Support\Id::make('gui-menu-group');
@endphp

{{-- Groups related items, like a set of dropdown.radio, under an optional heading that names the group. --}}
<div role="group" @if ($heading) aria-labelledby="{{ $id }}" @endif {{ $attributes }}>
    @if ($heading)
        <div id="{{ $id }}" class="px-2.5 pt-1.5 pb-1 text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ $heading }}</div>
    @endif
    {{ $slot }}
</div>
