@aware(['guiModal' => null])

@props([
    'for' => null,
])

{{-- Closes the nearest modal. Renders a GiraffeUI button, so it takes the same props. --}}
<x-gui::button {{ $attributes->merge(['commandfor' => $for ?? $guiModal['id'] ?? null, 'command' => 'close', 'variant' => 'ghost', 'color' => 'secondary']) }}>{{ $slot }}</x-gui::button>
