@props([
    'label' => null,
    'description' => null,
    'invalid' => null,
    'variant' => 'default',
])

@php
    $name = $attributes->get('name');
    $id = $attributes->get('id') ?? \JayAitch\GiraffeUI\Support\Form::id($name, $attributes->get('value')) ?? 'gui-'.\Illuminate\Support\Str::random(8);
    $errorKey = \JayAitch\GiraffeUI\Support\Form::errorKey($name, $attributes);
    $invalid ??= \JayAitch\GiraffeUI\Support\Form::error($errorKey, null, $errors ?? null) !== null;
    $hasLabel = $label !== null || ! \JayAitch\GiraffeUI\Support\Slot::isBlank($slot);
    $card = $variant === 'card';

    $input = $attributes
        ->except('aria-describedby')
        ->merge(array_filter([
            'id' => $id,
            'aria-invalid' => $invalid ? 'true' : null,
            'aria-describedby' => \JayAitch\GiraffeUI\Support\Form::describedBy($attributes->get('aria-describedby'), $description ? "{$id}-description" : null),
        ]))
        ->class([
            'size-4 shrink-0 cursor-pointer disabled:opacity-50 rounded-full accent-gui-primary disabled:cursor-not-allowed dark:scheme-dark',
            // A card shows focus and errors on its border instead.
            'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text aria-invalid:outline-2 aria-invalid:outline-offset-2 aria-invalid:outline-gui-danger-text' => ! $card,
            'mt-0.5 focus-visible:outline-none' => $card,
        ]);
@endphp

@if ($card)
    {{-- The whole card is the label, so clicking anywhere on it picks the option. --}}
    <label for="{{ $id }}" class="group/choice flex cursor-pointer gap-3 rounded-gui border border-zinc-200 bg-white p-4 text-sm leading-5 shadow-xs transition-colors hover:bg-zinc-50 has-checked:border-gui-primary-text has-checked:bg-gui-primary/5 has-checked:ring-1 has-checked:ring-gui-primary-text has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-gui-primary-text has-aria-invalid:border-gui-danger-text has-disabled:cursor-not-allowed has-disabled:bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:bg-zinc-800/60 dark:has-checked:bg-gui-primary/10 dark:has-disabled:bg-zinc-900">
        <input type="radio" {{ $input }}>
        <span class="min-w-0 flex-1">
            @if ($hasLabel)
                <span class="block font-medium text-zinc-900 group-has-disabled/choice:text-zinc-500 dark:text-zinc-100 dark:group-has-disabled/choice:text-zinc-400">{{ $label ?? $slot }}</span>
            @endif
            @if ($description)
                <span id="{{ $id }}-description" class="mt-0.5 block text-zinc-600 group-has-disabled/choice:text-zinc-500 dark:text-zinc-400">{{ $description }}</span>
            @endif
        </span>
    </label>
@else
    <div class="group/choice flex gap-3">
        <div class="flex h-5 items-center">
            <input type="radio" {{ $input }}>
        </div>

        @if ($hasLabel || $description)
            <div class="text-sm leading-5">
                @if ($hasLabel)
                    <label for="{{ $id }}" class="font-medium text-zinc-900 group-has-disabled/choice:text-zinc-500 dark:text-zinc-100 dark:group-has-disabled/choice:text-zinc-400">{{ $label ?? $slot }}</label>
                @endif
                @if ($description)
                    <p id="{{ $id }}-description" class="text-zinc-600 group-has-disabled/choice:text-zinc-500 dark:text-zinc-400 dark:group-has-disabled/choice:text-zinc-400">{{ $description }}</p>
                @endif
            </div>
        @endif
    </div>
@endif
