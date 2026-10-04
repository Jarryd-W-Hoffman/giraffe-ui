@aware(['guiField' => null])

@props([
    'type' => 'text',
    'size' => 'md',
    'invalid' => null,
])

@php
    $control = \JayAitch\GiraffeUI\Support\Form::control($guiField, $attributes, $errors ?? null, $invalid);
    $value = \JayAitch\GiraffeUI\Support\Form::value($attributes, $control['errorKey'], $type);

    $sizes = [
        'sm' => 'h-8 px-2.5 text-sm',
        'md' => 'h-10 px-3 text-sm',
        'lg' => 'h-12 px-3.5 text-base',
    ];

    $hasLeading = isset($leading);
    $hasTrailing = isset($trailing);

    // With leading or trailing content, those parts are pinned to the input itself (anchor positioning), so width
    // classes on the input move them too.
    $anchorId = ($hasLeading || $hasTrailing) ? ($control['attributes']['id'] ?? 'gui-input-'.\Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(8))) : null;

    $input = $attributes
        ->except(['aria-describedby', 'value'])
        ->merge($control['attributes'] + ['type' => $type] + ($value !== null ? ['value' => $value] : []))
        ->class([
            'block w-full min-w-0 rounded-gui border border-gui-input bg-white text-zinc-900 shadow-xs transition-colors',
            'placeholder:text-zinc-500 dark:placeholder:text-zinc-400',
            'focus:outline-2 focus:-outline-offset-1 focus:outline-gui-primary-text',
            'aria-invalid:border-gui-danger-text aria-invalid:focus:outline-gui-danger-text',
            'disabled:cursor-not-allowed disabled:bg-zinc-100 disabled:text-zinc-500 dark:disabled:bg-zinc-800 dark:disabled:text-zinc-400',
            'dark:bg-zinc-900 dark:text-zinc-100 dark:scheme-dark',
            'file:mr-3 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-zinc-900 dark:file:text-zinc-100',
            $sizes[$size] ?? $sizes['md'],
            'pl-9' => $hasLeading,
            'pr-9' => $hasTrailing,
        ])
        ->when($anchorId, fn ($input) => $input->style('anchor-name: '.\JayAitch\GiraffeUI\Support\Anchor::name($anchorId)));
@endphp

@if ($hasLeading || $hasTrailing)
    <div class="relative">
        @if ($hasLeading)
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-zinc-500 dark:text-zinc-400" style="{{ \JayAitch\GiraffeUI\Support\Anchor::pin($anchorId, 'left') }}">{{ $leading }}</div>
        @endif
        <input {{ $input }}>
        @if ($hasTrailing)
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-500 dark:text-zinc-400" style="{{ \JayAitch\GiraffeUI\Support\Anchor::pin($anchorId, 'right') }}">{{ $trailing }}</div>
        @endif
    </div>
@else
    <input {{ $input }}>
@endif
