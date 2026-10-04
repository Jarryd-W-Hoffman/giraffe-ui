@aware(['guiField' => null])

@props([
    'options' => null,
    'placeholder' => null,
    'size' => 'md',
    'invalid' => null,
])

@php
    $control = \JayAitch\GiraffeUI\Support\Form::control($guiField, $attributes, $errors ?? null, $invalid);
    $selected = \JayAitch\GiraffeUI\Support\Form::value($attributes, $control['errorKey']);
    $isSelected = fn ($value) => $selected !== null && in_array((string) $value, array_map('strval', (array) $selected), true);

    $sizes = [
        'sm' => 'h-8 pl-2.5 text-sm',
        'md' => 'h-10 pl-3 text-sm',
        'lg' => 'h-12 pl-3.5 text-base',
    ];
@endphp

<div class="relative">
    <select {{ $attributes
        ->except(['aria-describedby', 'value'])
        ->merge($control['attributes'])
        ->class([
            'block w-full min-w-0 cursor-pointer appearance-none rounded-gui border border-gui-input bg-white pr-9 text-zinc-900 shadow-xs transition-colors',
            'focus:outline-2 focus:-outline-offset-1 focus:outline-gui-primary-text',
            'aria-invalid:border-gui-danger-text aria-invalid:focus:outline-gui-danger-text',
            'disabled:cursor-not-allowed disabled:bg-zinc-100 disabled:text-zinc-500 dark:disabled:bg-zinc-800 dark:disabled:text-zinc-400',
            'dark:bg-zinc-900 dark:text-zinc-100 dark:scheme-dark',
            $sizes[$size] ?? $sizes['md'],
        ]) }}>
        @if ($placeholder)
            <option value="" disabled @selected($selected === null || $selected === '')>{{ $placeholder }}</option>
        @endif

        @if (is_iterable($options))
            @foreach ($options as $value => $label)
                <option value="{{ $value }}" @selected($isSelected($value))>{{ $label }}</option>
            @endforeach
        @endif

        {{ $slot }}
    </select>

    <x-gui::icon name="chevron-down" size="sm" class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-zinc-500 dark:text-zinc-400" />
</div>
