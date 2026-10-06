@props([
    'label' => null,
])

{{--
    A list of events in order: an order's history, an activity feed, a changelog. It's an ordered list, so screen
    readers say how many events there are and where each one sits. Put x-gui::timeline.item inside, oldest or newest
    first: the list keeps your order.
--}}
<ol @if ($label) aria-label="{{ $label }}" @endif {{ $attributes->class('flex flex-col') }}>
    {{ $slot }}
</ol>
