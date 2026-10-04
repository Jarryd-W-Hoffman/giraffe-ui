@props([
    'name',
    'size' => 'md',
    'label' => null,
])

@php
    $sizes = [
        'xs' => 'size-3.5',
        'sm' => 'size-4',
        'md' => 'size-5',
        'lg' => 'size-6',
    ];

    $classes = ($sizes[$size] ?? $sizes['md']).' shrink-0';

    // Decorative unless labelled: a labelled icon is announced as an image.
    $a11y = $label !== null ? ['role' => 'img', 'aria-label' => $label] : ['aria-hidden' => 'true'];
@endphp

@if (\JayAitch\GiraffeUI\Support\Icons::has($name))
    <svg viewBox="0 0 20 20" fill="currentColor" {{ $attributes->merge($a11y)->class($classes) }}>
        <path fill-rule="evenodd" clip-rule="evenodd" d="{{ \JayAitch\GiraffeUI\Support\Icons::path($name) }}" />
    </svg>
@elseif (function_exists('svg'))
    {{-- Any blade-icons set: <x-gui::icon name="heroicon-o-bell" />, <x-gui::icon name="lucide-rocket" />. --}}
    {{ svg($name, $attributes->class($classes)->get('class'), $attributes->except('class')->merge($a11y)->getAttributes()) }}
@else
    @php
        throw new InvalidArgumentException("GiraffeUI doesn't include an icon named [{$name}]. Install blade-ui-kit/blade-icons with an icon set to use any icon, or use one of: ".implode(', ', array_keys(\JayAitch\GiraffeUI\Support\Icons::PATHS)).'.');
    @endphp
@endif
