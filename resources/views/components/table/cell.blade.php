@props([
    'wrap' => false,
])

<td {{ $attributes->class(['px-4 py-3', 'whitespace-nowrap' => ! $wrap]) }}>{{ $slot }}</td>
