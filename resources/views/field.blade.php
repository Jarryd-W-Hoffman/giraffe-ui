@php
    $message = \JayAitch\GiraffeUI\Support\Form::error(\JayAitch\GiraffeUI\Support\Form::errorKey($name), $error, $errors ?? null);
@endphp

<div {{ $attributes->class('space-y-1.5') }}>
    @if ($label)
        <x-gui::label :for="$id" id="{{ $id }}-label" :required="$required">{{ $label }}</x-gui::label>
    @endif

    {{ $slot }}

    @if ($hint)
        <p id="{{ $id }}-hint" class="text-sm text-zinc-600 dark:text-zinc-400">{{ $hint }}</p>
    @endif

    @if ($message)
        <p id="{{ $id }}-error" class="text-sm font-medium text-gui-danger-text">{{ $message }}</p>
    @endif
</div>
