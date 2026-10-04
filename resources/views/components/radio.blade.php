@props([
    'label' => null,
    'description' => null,
    'invalid' => null,
])

@php
    $name = $attributes->get('name');
    $id = $attributes->get('id') ?? \JayAitch\GiraffeUI\Support\Form::id($name, $attributes->get('value')) ?? 'gui-'.\Illuminate\Support\Str::random(8);
    $errorKey = \JayAitch\GiraffeUI\Support\Form::errorKey($name, $attributes);
    $invalid ??= \JayAitch\GiraffeUI\Support\Form::error($errorKey, null, $errors ?? null) !== null;
    $hasLabel = $label !== null || ! \JayAitch\GiraffeUI\Support\Slot::isBlank($slot);
@endphp

<div class="flex gap-3 has-disabled:opacity-60">
    <div class="flex h-5 items-center">
        <input type="radio" {{ $attributes
            ->except('aria-describedby')
            ->merge(array_filter([
                'id' => $id,
                'aria-invalid' => $invalid ? 'true' : null,
                'aria-describedby' => \JayAitch\GiraffeUI\Support\Form::describedBy($attributes->get('aria-describedby'), $description ? "{$id}-description" : null),
            ]))
            ->class([
                'size-4 shrink-0 cursor-pointer rounded-full accent-gui-primary disabled:cursor-not-allowed dark:scheme-dark',
                'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text',
                'aria-invalid:outline-2 aria-invalid:outline-offset-2 aria-invalid:outline-gui-danger-text',
            ]) }}>
    </div>

    @if ($hasLabel || $description)
        <div class="text-sm leading-5">
            @if ($hasLabel)
                <label for="{{ $id }}" class="font-medium text-zinc-900 dark:text-zinc-100">{{ $label ?? $slot }}</label>
            @endif
            @if ($description)
                <p id="{{ $id }}-description" class="text-zinc-600 dark:text-zinc-400">{{ $description }}</p>
            @endif
        </div>
    @endif
</div>
