@aware(['guiTabs' => null])

@props([
    'name',
])

@php
    $prefix = $guiTabs['id'] ?? 'gui-tabs';
    $slug = \Illuminate\Support\Str::slug($name);
    $selected = ($guiTabs['selected'] ?? null) === $name;
@endphp

{{-- Hidden on the server too, so there's no flash of every panel before Alpine starts. --}}
<div
    role="tabpanel"
    id="{{ $prefix }}-panel-{{ $slug }}"
    aria-labelledby="{{ $prefix }}-tab-{{ $slug }}"
    tabindex="0"
    @unless ($selected) hidden @endunless
    x-bind:hidden="selected !== @js($name)"
    {{ $attributes->class('pt-4 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text') }}
>
    {{ $slot }}
</div>
