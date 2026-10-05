@aware(['guiField' => null])

@props([
    'length' => 6,
    'size' => 'md',
    'invalid' => null,
    'alphanumeric' => false,
    'autosubmit' => false,
])

@php
    $control = \JayAitch\GiraffeUI\Support\Form::control($guiField, $attributes, $errors ?? null, $invalid);
    $length = max(1, (int) $length);

    // Every class is written out in full so Tailwind can find it when scanning this file.
    $boxes = [
        'sm' => 'size-9 text-base',
        'md' => 'size-11 text-lg',
        'lg' => 'size-13 text-xl',
    ];
    $plain = [
        'sm' => 'h-9 px-2.5 text-base',
        'md' => 'h-11 px-3 text-lg',
        'lg' => 'h-13 px-3.5 text-xl',
    ];
@endphp

{{--
    A one-time code or PIN. It is one real input (autocomplete="one-time-code"), so phone autofill, pasting, a single
    tab stop and a single submitted value all just work. Alpine draws the boxes from its value, over which the input
    sits invisibly; without JavaScript it's a plain text field. Codes are never refilled from old input.
--}}
<div
    class="group/otp relative inline-flex max-w-full"
    x-data="{
        value: '',
        focused: false,
        length: @js($length),
        init() {
            const input = this.$refs.input;
            const value = Object.getOwnPropertyDescriptor(HTMLInputElement.prototype, 'value');

            // wire:model, x-model and scripts set the value without an input event, so redraw for those too.
            Object.defineProperty(input, 'value', {
                configurable: true,
                get: () => value.get.call(input),
                set: (code) => {
                    value.set.call(input, code);
                    this.value = value.get.call(input);
                },
            });
            this.value = input.value;
            // maxlength would cut a pasted '12-34-56' to '12-34-' before the dashes are dropped; typed() limits the length.
            input.removeAttribute('maxlength');
        },
        // Keep only the characters a code can have, up to its length, and submit once it's complete if asked.
        typed(input) {
            const clean = input.value.replace(new RegExp(@js($alphanumeric ? '[^a-z0-9]' : '[^0-9]'), 'gi'), '').toUpperCase().slice(0, this.length);
            if (clean !== input.value) input.value = clean;
            this.value = clean;
            if (@js((bool) $autosubmit) && clean.length === this.length) input.form?.requestSubmit();
        },
        // The box the next character goes in.
        get active() {
            return Math.min(this.value.length, this.length - 1);
        },
    }"
>
    <input
        type="text"
        x-ref="input"
        x-bind:data-enhanced="true"
        inputmode="{{ $alphanumeric ? 'text' : 'numeric' }}"
        autocomplete="one-time-code"
        autocapitalize="characters"
        spellcheck="false"
        maxlength="{{ $length }}"
        size="{{ $length * 2 }}"
        pattern="{{ $alphanumeric ? '[A-Za-z0-9]' : '[0-9]' }}{{ '{'.$length.'}' }}"
        x-on:input="typed($el)"
        x-on:focus="focused = true"
        x-on:blur="focused = false"
        {{ $attributes
            ->except(['aria-describedby', 'value'])
            ->merge($control['attributes'])
            ->class([
                'block rounded-gui border border-gui-input bg-white font-mono tracking-[0.5em] text-zinc-900 shadow-xs transition-colors tabular-nums',
                'focus:outline-2 focus:-outline-offset-1 focus:outline-gui-primary-text',
                'aria-invalid:border-gui-danger-text aria-invalid:focus:outline-gui-danger-text',
                'disabled:cursor-not-allowed disabled:bg-zinc-100 disabled:text-zinc-500 dark:disabled:bg-zinc-800 dark:disabled:text-zinc-400',
                'dark:bg-zinc-900 dark:text-zinc-100',
                $plain[$size] ?? $plain['md'],
                // Once Alpine draws the boxes, the input covers them invisibly and takes the typing, clicks and paste.
                'data-enhanced:absolute data-enhanced:inset-0 data-enhanced:z-10 data-enhanced:h-full data-enhanced:w-full data-enhanced:cursor-text data-enhanced:border-0 data-enhanced:bg-transparent data-enhanced:text-transparent data-enhanced:caret-transparent data-enhanced:shadow-none data-enhanced:outline-none data-enhanced:selection:bg-transparent',
            ]) }}
    >

    <div aria-hidden="true" x-cloak class="flex gap-2">
        @for ($i = 0; $i < $length; $i++)
            <div
                x-bind:data-filled="value.length > {{ $i }}"
                x-bind:data-active="focused && active === {{ $i }}"
                @class([
                    'relative flex items-center justify-center rounded-gui border border-gui-input bg-white font-medium text-zinc-900 shadow-xs transition-colors tabular-nums dark:bg-zinc-900 dark:text-zinc-100',
                    'data-[active=true]:outline-2 data-[active=true]:-outline-offset-1 data-[active=true]:outline-gui-primary-text',
                    'group-has-aria-invalid/otp:border-gui-danger-text group-has-disabled/otp:bg-zinc-100 group-has-disabled/otp:text-zinc-500 dark:group-has-disabled/otp:bg-zinc-800',
                    $boxes[$size] ?? $boxes['md'],
                ])
            >
                <span x-text="value[{{ $i }}] ?? ''"></span>
                {{-- A blinking caret in the empty box that's next, while focused. --}}
                <span x-show="focused && active === {{ $i }} && value.length <= {{ $i }}" class="absolute h-1/2 w-px animate-pulse bg-current motion-reduce:animate-none"></span>
            </div>
        @endfor
    </div>
</div>
