@props([
    'label' => null,
])

@php
    $label ??= __('giraffeui::ui.main');
@endphp

<nav aria-label="{{ $label }}" {{ $attributes->class('flex flex-col gap-6') }}>
    {{ $slot }}
</nav>
