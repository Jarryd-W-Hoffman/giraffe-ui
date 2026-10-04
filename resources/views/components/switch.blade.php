@props([
    'label' => null,
    'description' => null,
])

@php
    $id = $attributes->get('id') ?? \JayAitch\GiraffeUI\Support\Form::id($attributes->get('name'), $attributes->get('value')) ?? 'gui-'.\Illuminate\Support\Str::random(8);
    $hasLabel = $label !== null || ! \JayAitch\GiraffeUI\Support\Slot::isBlank($slot);
@endphp

<div class="group flex gap-3 has-disabled:opacity-60">
    <div class="relative flex h-6 shrink-0 items-center">
        {{-- The real checkbox sits on top of the track, invisible, so clicks, focus and screen readers all use it. --}}
        <input type="checkbox" role="switch" {{ $attributes
            ->except('aria-describedby')
            ->merge(array_filter([
                'id' => $id,
                'aria-describedby' => \JayAitch\GiraffeUI\Support\Form::describedBy($attributes->get('aria-describedby'), $description ? "{$id}-description" : null),
            ]))
            ->class('peer absolute inset-0 z-10 size-full cursor-pointer appearance-none rounded-full opacity-0 disabled:cursor-not-allowed') }}>
        <span aria-hidden="true" class="flex h-6 w-11 items-center rounded-full bg-gui-input p-0.5 transition-colors peer-checked:bg-gui-primary peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-gui-primary-text">
            <span class="size-5 rounded-full bg-white shadow-sm transition-transform group-has-checked:translate-x-5"></span>
        </span>
    </div>

    @if ($hasLabel || $description)
        <div class="text-sm leading-6">
            @if ($hasLabel)
                <label for="{{ $id }}" class="font-medium text-zinc-900 dark:text-zinc-100">{{ $label ?? $slot }}</label>
            @endif
            @if ($description)
                <p id="{{ $id }}-description" class="leading-5 text-zinc-600 dark:text-zinc-400">{{ $description }}</p>
            @endif
        </div>
    @endif
</div>
