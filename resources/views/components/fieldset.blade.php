@props([
    'legend' => null,
    'hint' => null,
    'name' => null,
    'error' => null,
])

@php
    $id = $attributes->get('id') ?? \JayAitch\GiraffeUI\Support\Form::id($name, 'fieldset') ?? \JayAitch\GiraffeUI\Support\Id::make('gui');
    $message = \JayAitch\GiraffeUI\Support\Form::error(\JayAitch\GiraffeUI\Support\Form::errorKey($name), $error, $errors ?? null);
@endphp

<fieldset {{ $attributes
    ->except('aria-describedby')
    ->merge(array_filter([
        'id' => $id,
        'aria-describedby' => \JayAitch\GiraffeUI\Support\Form::describedBy($attributes->get('aria-describedby'), $hint ? "{$id}-hint" : null, $message ? "{$id}-error" : null),
    ]))
    ->class('min-w-0') }}>
    @if ($legend)
        <legend class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $legend }}</legend>
    @endif
    @if ($hint)
        <p id="{{ $id }}-hint" class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ $hint }}</p>
    @endif

    <div class="mt-3 space-y-3">{{ $slot }}</div>

    @if ($message)
        <p id="{{ $id }}-error" class="mt-3 text-sm font-medium text-gui-danger-text">{{ $message }}</p>
    @endif
</fieldset>
