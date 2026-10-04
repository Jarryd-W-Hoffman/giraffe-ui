@aware(['guiField' => null])

@props([
    'size' => 'md',
    'invalid' => null,
    'decrementLabel' => 'Decrease',
    'incrementLabel' => 'Increase',
])

@php
    $control = \JayAitch\GiraffeUI\Support\Form::control($guiField, $attributes, $errors ?? null, $invalid);
    $id = $control['attributes']['id'] ?? \JayAitch\GiraffeUI\Support\Id::make('gui-number');
    $value = \JayAitch\GiraffeUI\Support\Form::value($attributes, $control['errorKey'], 'number');
    $disabled = $attributes->has('disabled') && $attributes->get('disabled') !== false;

    $sizes = [
        'sm' => ['input' => 'h-8 px-9 text-sm', 'button' => 'w-8'],
        'md' => ['input' => 'h-10 px-10 text-sm', 'button' => 'w-10'],
        'lg' => ['input' => 'h-12 px-12 text-base', 'button' => 'w-12'],
    ];
    $sized = $sizes[$size] ?? $sizes['md'];

    $button = 'absolute inset-y-0 flex items-center justify-center text-zinc-600 transition-colors hover:text-zinc-900 disabled:pointer-events-none disabled:text-zinc-400 dark:text-zinc-400 dark:hover:text-zinc-100 dark:disabled:text-zinc-600 '.$sized['button'];
@endphp

{{--
    A native number input: arrow keys step it, and step, min and max are the browser's. Alpine adds − and + buttons
    that call stepDown() and stepUp(). They stay out of the tab order, since the arrow keys already do the same, and
    are pinned to the input with anchor positioning so width classes on the input move them too.
--}}
<div
    class="relative"
    x-data="{
        value: null,
        min: null,
        max: null,

        init() {
            const input = this.$refs.input;
            const value = Object.getOwnPropertyDescriptor(HTMLInputElement.prototype, 'value');

            // wire:model, x-model and scripts set the value without an input event, so watch those too.
            Object.defineProperty(input, 'value', {
                configurable: true,
                get: () => value.get.call(input),
                set: (number) => {
                    value.set.call(input, number);
                    this.sync();
                },
            });
            this.sync();
        },
        sync() {
            const input = this.$refs.input;
            this.value = input.value === '' ? null : Number(input.value);
            this.min = input.min === '' ? null : Number(input.min);
            this.max = input.max === '' ? null : Number(input.max);
        },
        step(direction) {
            const input = this.$refs.input;
            direction > 0 ? input.stepUp() : input.stepDown();
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
            this.sync();
        },
    }"
>
    <input
        type="number"
        x-ref="input"
        x-bind:data-enhanced="true"
        x-on:input="sync()"
        {{ $attributes
            ->except(['aria-describedby', 'value', 'id'])
            ->merge($control['attributes'] + ['id' => $id] + ($value !== null ? ['value' => $value] : []))
            ->class([
                'block w-full min-w-0 rounded-gui border border-gui-input bg-white text-center text-zinc-900 tabular-nums shadow-xs transition-colors',
                'placeholder:text-zinc-500 dark:placeholder:text-zinc-400',
                'focus:outline-2 focus:-outline-offset-1 focus:outline-gui-primary-text',
                'aria-invalid:border-gui-danger-text aria-invalid:focus:outline-gui-danger-text',
                'disabled:cursor-not-allowed disabled:bg-zinc-100 disabled:text-zinc-500 dark:disabled:bg-zinc-800 dark:disabled:text-zinc-400',
                'dark:bg-zinc-900 dark:text-zinc-100 dark:scheme-dark',
                // Our buttons replace the browser's spinners, but only once Alpine is running.
                'data-enhanced:[appearance:textfield] data-enhanced:[&::-webkit-inner-spin-button]:appearance-none data-enhanced:[&::-webkit-outer-spin-button]:appearance-none',
                $sized['input'],
            ])
            ->style('anchor-name: '.\JayAitch\GiraffeUI\Support\Anchor::name($id)) }}
    >

    <button
        type="button"
        tabindex="-1"
        x-cloak
        aria-label="{{ $decrementLabel }}"
        style="{{ \JayAitch\GiraffeUI\Support\Anchor::pin($id, 'left') }}"
        aria-controls="{{ $id }}"
        x-on:click="step(-1)"
        x-bind:disabled="{{ $disabled ? 'true' : 'min !== null && value !== null && value <= min' }}"
        @disabled($disabled)
        class="{{ $button }} left-0 rounded-l-gui"
    >
        <x-gui::icon name="minus" size="sm" />
    </button>
    <button
        type="button"
        tabindex="-1"
        x-cloak
        aria-label="{{ $incrementLabel }}"
        style="{{ \JayAitch\GiraffeUI\Support\Anchor::pin($id, 'right') }}"
        aria-controls="{{ $id }}"
        x-on:click="step(1)"
        x-bind:disabled="{{ $disabled ? 'true' : 'max !== null && value !== null && value >= max' }}"
        @disabled($disabled)
        class="{{ $button }} right-0 rounded-r-gui"
    >
        <x-gui::icon name="plus" size="sm" />
    </button>
</div>
