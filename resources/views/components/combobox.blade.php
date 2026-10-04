@aware(['guiField' => null])

@props([
    'options' => [],
    'value' => null,
    'placeholder' => null,
    'size' => 'md',
    'invalid' => null,
    'empty' => 'No results',
])

@php
    // wire:model and x-model bind the value on the root (x-modelable); everything else goes on the text input.
    $binding = $attributes->filter(fn ($v, $key) => str_starts_with($key, 'wire:model') || str_starts_with($key, 'x-model'));
    $inputAttributes = $attributes->except(array_keys($binding->getAttributes()));

    $control = \JayAitch\GiraffeUI\Support\Form::control($guiField, $inputAttributes, $errors ?? null, $invalid);
    $name = $control['name'];
    $id = $control['attributes']['id'] ?? 'gui-combobox-'.\Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(8));
    $selected = $value ?? ($name && $binding->isEmpty() ? old($control['errorKey']) : null);

    $sizes = [
        'sm' => 'h-8 pl-2.5 text-sm',
        'md' => 'h-10 pl-3 text-sm',
        'lg' => 'h-12 pl-3.5 text-base',
    ];
@endphp

{{-- WAI-ARIA combobox with a listbox popup. Needs Alpine (Livewire includes it). --}}
<div
    class="relative"
    style="anchor-name: {{ \JayAitch\GiraffeUI\Support\Anchor::name($id) }}"
    x-data="{
        open: false,
        query: '',
        // Only filter once the user types; reopening shows every option with the current one highlighted.
        filtering: false,
        value: @js($selected === null ? '' : (string) $selected),
        active: null,

        init() {
            this.query = this.labelFor(this.value);
            this.$watch('value', (value) => { if (! this.open) this.query = this.labelFor(value); });
        },
        options() {
            return [...this.$refs.listbox.querySelectorAll('[role=option]')];
        },
        matches(option) {
            return ! this.filtering || option.dataset.label.toLowerCase().includes(this.query.trim().toLowerCase());
        },
        visible() {
            return this.options().filter((option) => this.matches(option));
        },
        labelFor(value) {
            return this.options().find((option) => option.dataset.value === String(value))?.dataset.label ?? '';
        },
        show() {
            if (this.open) return;
            this.open = true;
            this.$refs.popup.showPopover?.();
            this.active = this.options().find((option) => option.dataset.value === String(this.value))?.id ?? null;
        },
        hide() {
            if (! this.open) return;
            this.open = false;
            this.filtering = false;
            this.$refs.popup.hidePopover?.();
            this.active = null;
            this.query = this.labelFor(this.value);
        },
        move(step) {
            // Opening lands on the selected option rather than stepping past it.
            if (! this.open) {
                this.show();
                if (this.active) {
                    this.$nextTick(() => document.getElementById(this.active)?.scrollIntoView({ block: 'nearest' }));
                    return;
                }
            }
            const options = this.visible();
            if (! options.length) return;
            let index = options.findIndex((option) => option.id === this.active);
            index = index === -1 ? (step > 0 ? 0 : options.length - 1) : (index + step + options.length) % options.length;
            this.active = options[index].id;
            options[index].scrollIntoView({ block: 'nearest' });
        },
        choose(option) {
            if (! option || option.getAttribute('aria-disabled') === 'true') return;
            this.value = option.dataset.value;
            this.hide();
            this.$nextTick(() => this.$refs.hidden.dispatchEvent(new Event('change', { bubbles: true })));
        },
        // The highlighted option, or the first match if the highlight is stale (e.g. Enter straight after typing).
        current() {
            const option = document.getElementById(this.active);
            return option && this.matches(option) ? option : this.visible()[0];
        },
        typed() {
            this.filtering = true;
            this.show();
            this.active = this.visible()[0]?.id ?? null;
        },
    }"
    x-modelable="value"
    x-on:click.outside="hide()"
    {{ $binding }}
>
    @if ($name)
        <input type="hidden" name="{{ $name }}" x-ref="hidden" x-bind:value="value" value="{{ $selected }}">
    @else
        <input type="hidden" x-ref="hidden" x-bind:value="value">
    @endif

    <input
        type="text"
        role="combobox"
        autocomplete="off"
        aria-autocomplete="list"
        aria-controls="{{ $id }}-listbox"
        aria-expanded="false"
        x-bind:aria-expanded="open"
        x-bind:aria-activedescendant="active"
        x-model="query"
        x-on:input="typed()"
        x-on:click="open ? hide() : show()"
        x-on:keydown.down.prevent="$event.altKey ? show() : move(1)"
        x-on:keydown.up.prevent="move(-1)"
        x-on:keydown.enter="if (open) { $event.preventDefault(); choose(current()); }"
        x-on:keydown.escape.prevent="open ? hide() : (value = '', query = '')"
        x-on:keydown.tab="hide()"
        x-on:blur="hide()"
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        {{ $inputAttributes
            ->except(['aria-describedby', 'name', 'value', 'id'])
            ->merge(array_diff_key($control['attributes'], ['name' => true]) + ['id' => $id])
            ->class([
                'block w-full min-w-0 rounded-gui border border-gui-input bg-white pr-9 text-zinc-900 shadow-xs transition-colors',
                'placeholder:text-zinc-500 dark:placeholder:text-zinc-400',
                'focus:outline-2 focus:-outline-offset-1 focus:outline-gui-primary-text',
                'aria-invalid:border-gui-danger-text aria-invalid:focus:outline-gui-danger-text',
                'disabled:cursor-not-allowed disabled:bg-zinc-100 disabled:text-zinc-500 dark:disabled:bg-zinc-800 dark:disabled:text-zinc-400',
                'dark:bg-zinc-900 dark:text-zinc-100 dark:scheme-dark',
                $sizes[$size] ?? $sizes['md'],
            ]) }}
    >

    <x-gui::icon name="chevron-up-down" size="sm" class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-zinc-500 dark:text-zinc-400" />

    {{-- A manual popover in the top layer, so the list isn't clipped by modals or scrolling containers. --}}
    <div
        x-ref="popup"
        popover="manual"
        style="position-anchor: {{ \JayAitch\GiraffeUI\Support\Anchor::name($id) }}"
        x-on:mousedown.prevent
        class="inset-auto m-0 my-1 max-h-60 w-[anchor-size(width)] overflow-y-auto rounded-gui border border-zinc-200 bg-white p-1 text-sm text-zinc-900 shadow-lg [position-area:bottom_span-right] [position-try-fallbacks:flip-block] dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-100"
    >
        <ul id="{{ $id }}-listbox" role="listbox" x-ref="listbox">
            @foreach ($options as $optionValue => $optionLabel)
                <li
                    id="{{ $id }}-option-{{ $loop->index }}"
                    role="option"
                    data-value="{{ $optionValue }}"
                    data-label="{{ $optionLabel }}"
                    aria-selected="{{ (string) $optionValue === (string) $selected ? 'true' : 'false' }}"
                    x-bind:aria-selected="value === @js((string) $optionValue)"
                    x-show="matches($el)"
                    x-on:click="choose($el)"
                    x-on:mousemove="active = $el.id"
                    x-bind:data-active="active === $el.id"
                    class="group/option flex cursor-pointer items-center justify-between gap-2 rounded-[calc(var(--radius-gui)-2px)] px-2.5 py-1.5 data-[active=true]:bg-zinc-100 dark:data-[active=true]:bg-zinc-800"
                >
                    {{ $optionLabel }}
                    <x-gui::icon name="check" size="sm" class="invisible text-gui-primary-text group-aria-selected/option:visible" />
                </li>
            @endforeach
        </ul>
        <p x-show="open && visible().length === 0" class="px-2.5 py-1.5 text-zinc-500 dark:text-zinc-400" role="status">{{ $empty }}</p>
    </div>
</div>
