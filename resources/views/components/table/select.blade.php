@props([
    'label' => 'Select row',
])

{{--
    A row's selection checkbox, in its own cell. It's a real checkbox: give it name="selected[]" and a value to submit
    with a form, or wire:model / x-model. Attributes go on the checkbox. table.select-all in the header follows it.
--}}
<td
    class="w-px px-4 py-3"
    x-data="{
        // wire:model, x-model and scripts set checked without an event; tell table.select-all anyway.
        init() {
            const box = this.$el.querySelector('input');
            const checked = Object.getOwnPropertyDescriptor(HTMLInputElement.prototype, 'checked');
            Object.defineProperty(box, 'checked', {
                configurable: true,
                get: () => checked.get.call(box),
                set: (value) => {
                    checked.set.call(box, value);
                    box.dispatchEvent(new CustomEvent('gui-select-sync', { bubbles: true }));
                },
            });
        },
    }"
>
    <input
        type="checkbox"
        data-gui-select
        aria-label="{{ $label }}"
        {{ $attributes->class('block size-4 shrink-0 cursor-pointer rounded-sm accent-gui-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text disabled:cursor-not-allowed disabled:opacity-50 dark:scheme-dark') }}
    >
</td>
