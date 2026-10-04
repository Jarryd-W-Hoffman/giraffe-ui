@props([
    'heading',
])

@php
    $headingId = \JayAitch\GiraffeUI\Support\Id::make('gui-cmd-group');
@endphp

{{-- Hidden when none of its items match the search. --}}
<div role="group" aria-labelledby="{{ $headingId }}" x-show="[...$el.querySelectorAll('[role=option]')].some((item) => matches(item))" {{ $attributes->class('py-1') }}>
    <div id="{{ $headingId }}" role="presentation" class="px-2 pt-2 pb-1 text-xs font-medium text-zinc-600 dark:text-zinc-400">{{ $heading }}</div>
    {{ $slot }}
</div>
