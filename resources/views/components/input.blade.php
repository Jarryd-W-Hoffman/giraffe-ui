@aware(['guiField' => null])

@props([
    'type' => 'text',
    'size' => 'md',
    'invalid' => null,
    'prefix' => null,
    'suffix' => null,
    'viewable' => false,
    'showLabel' => null,
    'copyable' => false,
])

@php
    $control = \JayAitch\GiraffeUI\Support\Form::control($guiField, $attributes, $errors ?? null, $invalid);
    $value = \JayAitch\GiraffeUI\Support\Form::value($attributes, $control['errorKey'], $type);

    // Every class is written out in full so Tailwind can find it when scanning this file.
    $sizes = [
        'sm' => 'h-8 px-2.5 text-sm',
        'md' => 'h-10 px-3 text-sm',
        'lg' => 'h-12 px-3.5 text-base',
    ];
    $addonSizes = [
        'sm' => 'px-2.5 text-sm',
        'md' => 'px-3 text-sm',
        'lg' => 'px-3.5 text-base',
    ];

    // A password with a Show button. The button replaces any trailing slot.
    $viewable = $viewable && $type === 'password';
    $showLabel ??= __('giraffeui::ui.show_password');
    $hasLeading = isset($leading);
    // Or a Copy button, for keys and links people copy rather than type.
    $copyable = $copyable && ! $viewable;
    $hasTrailing = isset($trailing) || $viewable || $copyable;
    $hasPrefix = $prefix !== null && $prefix !== '';
    $hasSuffix = $suffix !== null && $suffix !== '';

    // With leading or trailing content, those parts are pinned to the input itself (anchor positioning), so width
    // classes on the input move them too. Prefix and suffix text is read out after the label, via aria-describedby.
    $id = ($hasLeading || $hasTrailing || $hasPrefix || $hasSuffix)
        ? ($control['attributes']['id'] ?? \JayAitch\GiraffeUI\Support\Id::make('gui-input'))
        : ($control['attributes']['id'] ?? null);
    $anchorId = ($hasLeading || $hasTrailing) ? $id : null;
    $copyTarget = '[id="'.$id.'"]';
    $describedBy = \JayAitch\GiraffeUI\Support\Form::describedBy(
        $hasPrefix ? "{$id}-prefix" : null,
        $control['attributes']['aria-describedby'] ?? null,
        $hasSuffix ? "{$id}-suffix" : null,
    );

    $input = $attributes
        ->except(['aria-describedby', 'value'])
        ->merge(array_filter(['aria-describedby' => $describedBy, 'id' => $id]) + $control['attributes'] + ['type' => $type] + ($value !== null ? ['value' => $value] : []))
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
            'rounded-l-none' => $hasPrefix,
            'rounded-r-none' => $hasSuffix,
        ])
        ->when($anchorId, fn ($input) => $input->style('anchor-name: '.\JayAitch\GiraffeUI\Support\Anchor::name($anchorId)));

    $addon = 'flex shrink-0 items-center border border-gui-input bg-zinc-50 whitespace-nowrap text-zinc-600 dark:bg-zinc-800/60 dark:text-zinc-400 '.($addonSizes[$size] ?? $addonSizes['md']);
@endphp

@if ($hasLeading || $hasTrailing || $hasPrefix || $hasSuffix)
    <div
        @class(['relative', 'flex' => $hasPrefix || $hasSuffix])
        @if ($viewable)
            {{-- Show and hide the password. It goes back to hidden when the form submits, so the browser never saves it as text. --}}
            x-data="{ shown: false }"
            x-init="$refs.input.form?.addEventListener('submit', () => { shown = false; $refs.input.type = 'password'; })"
        @endif
    >
        @if ($hasPrefix)
            <span id="{{ $id }}-prefix" class="{{ $addon }} -mr-px rounded-l-gui">{{ $prefix }}</span>
        @endif
        @if ($hasLeading)
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-zinc-500 dark:text-zinc-400" style="{{ \JayAitch\GiraffeUI\Support\Anchor::pin($anchorId, 'left') }}">{{ $leading }}</div>
        @endif
        <input {{ $input }} @if ($viewable) x-ref="input" @endif>
        @if ($viewable)
            <div class="absolute inset-y-0 right-0 flex items-center pr-1.5" style="{{ \JayAitch\GiraffeUI\Support\Anchor::pin($anchorId, 'right', '0.375rem') }}">
                <button
                    type="button"
                    x-cloak
                    aria-label="{{ $showLabel }}"
                    aria-pressed="false"
                    x-bind:aria-pressed="shown.toString()"
                    x-on:click="shown = ! shown; $refs.input.type = shown ? 'text' : 'password'"
                    class="flex size-7 cursor-pointer items-center justify-center rounded-[calc(var(--radius-gui)-2px)] text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-gui-primary-text dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100"
                >
                    <x-gui::icon name="eye" size="sm" x-show="! shown" />
                    <x-gui::icon name="eye-slash" size="sm" x-show="shown" x-cloak />
                </button>
            </div>
        @elseif ($copyable)
            <div class="absolute inset-y-0 right-0 flex items-center pr-1" style="{{ \JayAitch\GiraffeUI\Support\Anchor::pin($anchorId, 'right', '0.25rem') }}">
                <x-gui::copy-button :target="$copyTarget" :size="$size === 'lg' ? 'sm' : 'xs'" />
            </div>
        @elseif ($hasTrailing)
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-500 dark:text-zinc-400" style="{{ \JayAitch\GiraffeUI\Support\Anchor::pin($anchorId, 'right') }}">{{ $trailing }}</div>
        @endif
        @if ($hasSuffix)
            <span id="{{ $id }}-suffix" class="{{ $addon }} -ml-px rounded-r-gui">{{ $suffix }}</span>
        @endif
    </div>
@else
    <input {{ $input }}>
@endif
