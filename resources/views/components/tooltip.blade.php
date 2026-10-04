@props([
    'text',
    'position' => 'top',
])

@php
    $id = $attributes->get('id') ?? 'gui-tooltip-'.\Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(8));
    $anchor = \JayAitch\GiraffeUI\Support\Anchor::name($id);
@endphp

{{--
    Shown with CSS on hover and keyboard focus, after a short delay. Hovering the tooltip itself keeps it open.
    Alpine links it to the trigger with aria-describedby and lets Escape dismiss it (WCAG 1.4.13).
--}}
<span
    class="group/tooltip inline-flex"
    style="anchor-name: {{ $anchor }}"
    x-data="{ dismissed: false }"
    x-init="$el.querySelector('a[href], button, input, select, textarea, [tabindex]')?.setAttribute('aria-describedby', @js($id))"
    x-on:keydown.escape="dismissed = true"
    x-on:mouseleave="dismissed = false"
    x-on:focusout="dismissed = false"
>
    {{ $slot }}
    <span
        id="{{ $id }}"
        role="tooltip"
        style="position-anchor: {{ $anchor }}"
        x-bind:class="dismissed && 'hidden'"
        {{ $attributes->except('id')->class([
            'invisible fixed z-50 w-max max-w-xs rounded-gui bg-zinc-900 px-2 py-1 text-xs font-medium text-white opacity-0 shadow-sm dark:bg-zinc-100 dark:text-zinc-900',
            'transition-[opacity,visibility] delay-100 duration-100 motion-reduce:transition-none',
            'group-hover/tooltip:visible group-hover/tooltip:opacity-100 group-hover/tooltip:delay-300',
            'group-has-focus-visible/tooltip:visible group-has-focus-visible/tooltip:opacity-100 group-has-focus-visible/tooltip:delay-0',
            \JayAitch\GiraffeUI\Support\Anchor::position($position),
        ]) }}
    >{{ $text }}</span>
</span>
