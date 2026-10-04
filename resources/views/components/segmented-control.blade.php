@props([
    'options' => [],
    'name' => null,
    'value' => null,
    'label' => null,
    'size' => 'md',
    'full' => false,
    'invalid' => null,
    'multiple' => false,
])

@php
    // Binding and state go on every input; everything else (class, id, aria-*) goes on the group.
    $perInput = fn ($v, $key) => str_starts_with($key, 'wire:model') || str_starts_with($key, 'x-model') || in_array($key, ['disabled', 'required', 'form'], true);
    $inputAttributes = $attributes->filter($perInput);
    $groupAttributes = $attributes->filter(fn ($v, $key) => ! $perInput($v, $key));

    $bound = $inputAttributes->filter(fn ($v, $key) => ! in_array($key, ['disabled', 'required', 'form'], true))->isNotEmpty();
    $errorKey = \JayAitch\GiraffeUI\Support\Form::errorKey($name, $attributes);
    $invalid ??= \JayAitch\GiraffeUI\Support\Form::error($errorKey, null, $errors ?? null) !== null
        || ($multiple && \JayAitch\GiraffeUI\Support\Form::error($errorKey ? $errorKey.'.*' : null, null, $errors ?? null) !== null);

    // Radios only form a group (one tab stop, arrow keys) when they share a name.
    $groupName = $name ?? $errorKey ?? \JayAitch\GiraffeUI\Support\Id::make('gui-segmented');
    $selected = $value ?? ($name && ! $bound ? old($errorKey) : null);

    // With multiple, each option is a checkbox submitted as name[]. required would demand every box, so it's dropped.
    if ($multiple) {
        $groupName = str_ends_with($groupName, '[]') ? $groupName : $groupName.'[]';
        $inputAttributes = $inputAttributes->except('required');
    }
    $isSelected = fn ($option) => $multiple
        ? in_array((string) $option, array_map('strval', (array) ($selected ?? [])), true)
        : $selected !== null && (string) $selected === (string) $option;

    $sizes = [
        'sm' => 'h-6 px-2.5 text-xs',
        'md' => 'h-8 px-3 text-sm',
        'lg' => 'h-10 px-4 text-sm',
    ];
@endphp

{{--
    Native radios, visually hidden: Tab reaches the group, arrow keys change the choice, and the form submits it.
    With multiple they are checkboxes in a group instead: each is a tab stop, and Space toggles it.
--}}
<div
    role="{{ $multiple ? 'group' : 'radiogroup' }}"
    {{ $groupAttributes
        ->merge(array_filter([
            'aria-label' => $label,
            'aria-invalid' => $invalid ? 'true' : null,
        ]))
        ->class([
            'gap-1 rounded-gui bg-zinc-100 p-1 dark:bg-zinc-800',
            'aria-invalid:outline-2 aria-invalid:outline-offset-1 aria-invalid:outline-gui-danger-text',
            'flex w-full' => $full,
            'inline-flex max-w-full' => ! $full,
        ]) }}
>
    @foreach ($options as $optionValue => $optionLabel)
        <label @class([
            'relative flex min-w-0 cursor-pointer items-center justify-center rounded-[calc(var(--radius-gui)-2px)] font-medium whitespace-nowrap text-zinc-600 transition-colors select-none',
            'hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100',
            // One pick always shows as raised. Independent toggles need a clearer difference, so they fill with the primary colour.
            'has-checked:bg-white has-checked:text-zinc-900 has-checked:shadow-xs dark:has-checked:bg-zinc-950 dark:has-checked:text-zinc-100' => ! $multiple,
            'has-checked:bg-gui-primary has-checked:text-gui-primary-foreground has-checked:shadow-xs has-checked:hover:text-gui-primary-foreground' => $multiple,
            'has-focus-visible:outline-2 has-focus-visible:outline-offset-1 has-focus-visible:outline-gui-primary-text',
            'has-disabled:cursor-not-allowed has-disabled:text-zinc-400 has-disabled:hover:text-zinc-400 dark:has-disabled:text-zinc-500 dark:has-disabled:hover:text-zinc-500',
            $sizes[$size] ?? $sizes['md'],
            'flex-1' => $full,
        ])>
            <input
                type="{{ $multiple ? 'checkbox' : 'radio' }}"
                name="{{ $groupName }}"
                value="{{ $optionValue }}"
                class="sr-only"
                @checked($isSelected($optionValue))
                {{ $inputAttributes }}
            >
            <span class="truncate">{{ $optionLabel }}</span>
        </label>
    @endforeach
</div>
