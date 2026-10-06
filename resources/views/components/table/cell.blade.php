@props([
    'wrap' => false,
    'align' => null,
])

@php
    // Every class is written out in full so Tailwind can find it when scanning this file.
    $alignments = ['left' => 'text-start', 'center' => 'text-center', 'right' => 'text-end'];
@endphp

<td {{ $attributes->class(['px-4 py-3', 'whitespace-nowrap' => ! $wrap, $alignments[$align] ?? null]) }}>{{ $slot }}</td>
