@aware(['guiField' => null])

@props([
    'invalid' => null,
    'showValue' => false,
    'prefix' => '',
    'suffix' => '',
    'marks' => [],
])

@php
    $control = \JayAitch\GiraffeUI\Support\Form::control($guiField, $attributes, $errors ?? null, $invalid);
    $id = $control['attributes']['id'] ?? \JayAitch\GiraffeUI\Support\Id::make('gui-slider');
    $value = \JayAitch\GiraffeUI\Support\Form::value($attributes, $control['errorKey'], 'range');

    // A range with no value sits halfway between min and max, which is what the browser does too.
    $min = (float) ($attributes->get('min') ?? 0);
    $max = (float) ($attributes->get('max') ?? 100);
    $shown = $value ?? ($min + $max) / 2;
    $format = fn ($number) => $prefix.(fmod((float) $number, 1.0) === 0.0 ? (int) $number : $number).$suffix;
@endphp

{{-- A native range input, coloured with accent-color. Marks become a datalist, which browsers can draw as ticks. --}}
<div
    @if ($showValue || $prefix !== '' || $suffix !== '')
        x-data="{
            value: @js((string) $shown),

            init() {
                const input = this.$refs.input;
                const value = Object.getOwnPropertyDescriptor(HTMLInputElement.prototype, 'value');

                // wire:model, x-model and scripts set the value without an input event, so follow those too.
                Object.defineProperty(input, 'value', {
                    configurable: true,
                    get: () => value.get.call(input),
                    set: (number) => {
                        value.set.call(input, number);
                        this.value = value.get.call(input);
                    },
                });
                this.value = input.value;
            },
        }"
    @endif
    @class(['flex items-center gap-3' => $showValue])
>
    <input
        type="range"
        x-ref="input"
        @if ($showValue || $prefix !== '' || $suffix !== '')
            x-on:input="value = $el.value"
        @endif
        @if ($prefix !== '' || $suffix !== '')
            {{-- Screen readers hear "$40" or "8 hours", not just the number. --}}
            aria-valuetext="{{ $format($shown) }}"
            x-bind:aria-valuetext="@js($prefix) + value + @js($suffix)"
        @endif
        @if ($marks) list="{{ $id }}-marks" @endif
        {{ $attributes
            ->except(['aria-describedby', 'value', 'id'])
            ->merge($control['attributes'] + ['id' => $id] + ($value !== null ? ['value' => $value] : []))
            ->class([
                'block h-5 w-full min-w-0 cursor-pointer accent-gui-primary disabled:cursor-not-allowed disabled:opacity-50 dark:scheme-dark',
                'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text',
                'aria-invalid:outline-2 aria-invalid:outline-offset-2 aria-invalid:outline-gui-danger-text',
            ]) }}
    >

    @if ($showValue)
        <output for="{{ $id }}" class="min-w-12 shrink-0 text-right text-sm font-medium text-zinc-900 tabular-nums dark:text-zinc-100" x-text="@js($prefix) + value + @js($suffix)">{{ $format($shown) }}</output>
    @endif

    @if ($marks)
        <datalist id="{{ $id }}-marks">
            @foreach ($marks as $mark => $markLabel)
                <option value="{{ is_int($mark) && ! is_string($markLabel) ? $markLabel : $mark }}" @if (is_string($markLabel)) label="{{ $markLabel }}" @endif></option>
            @endforeach
        </datalist>
    @endif
</div>
