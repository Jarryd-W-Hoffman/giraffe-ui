@aware(['guiField' => null])

@props([
    'rows' => 3,
    'invalid' => null,
])

@php
    $control = \JayAitch\GiraffeUI\Support\Form::control($guiField, $attributes, $errors ?? null, $invalid);
    $value = ! \JayAitch\GiraffeUI\Support\Slot::isBlank($slot) ? $slot : \JayAitch\GiraffeUI\Support\Form::value($attributes, $control['errorKey']);
@endphp

<textarea {{ $attributes
    ->except(['aria-describedby', 'value'])
    ->merge($control['attributes'] + ['rows' => $rows])
    ->class([
        'block w-full min-w-0 resize-y rounded-gui border border-gui-input bg-white px-3 py-2 text-sm text-zinc-900 shadow-xs transition-colors',
        'placeholder:text-zinc-500 dark:placeholder:text-zinc-400',
        'focus:outline-2 focus:-outline-offset-1 focus:outline-gui-primary-text',
        'aria-invalid:border-gui-danger-text aria-invalid:focus:outline-gui-danger-text',
        'disabled:cursor-not-allowed disabled:bg-zinc-100 disabled:text-zinc-500 dark:disabled:bg-zinc-800 dark:disabled:text-zinc-400',
        'dark:bg-zinc-900 dark:text-zinc-100 dark:scheme-dark',
    ]) }}>{{ $value }}</textarea>
