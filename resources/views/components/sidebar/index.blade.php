@props([
    'label' => 'Main',
])

<nav aria-label="{{ $label }}" {{ $attributes->class('flex flex-col gap-6') }}>
    {{ $slot }}
</nav>
