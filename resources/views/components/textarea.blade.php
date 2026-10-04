@aware(['guiField' => null])

@props([
    'rows' => 3,
    'invalid' => null,
    'autosize' => false,
    'maxRows' => null,
    'counter' => false,
])

@php
    $control = \JayAitch\GiraffeUI\Support\Form::control($guiField, $attributes, $errors ?? null, $invalid);
    $value = ! \JayAitch\GiraffeUI\Support\Slot::isBlank($slot) ? $slot : \JayAitch\GiraffeUI\Support\Form::value($attributes, $control['errorKey']);

    $id = $control['attributes']['id'] ?? ($counter ? \JayAitch\GiraffeUI\Support\Id::make('gui-textarea') : null);
    $max = is_numeric($attributes->get('maxlength')) ? (int) $attributes->get('maxlength') : null;

    // Count the way the browser does: UTF-16 code units, with the slot's HTML entities decoded.
    $text = str_replace("\r\n", "\n", html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5));
    $length = intdiv(strlen(mb_convert_encoding($text, 'UTF-16LE', 'UTF-8')), 2);

    // field-sizing: content grows the box with its text. rows becomes the minimum (plus padding and border),
    // so browsers without field-sizing just show the usual fixed height.
    $edge = '1rem + 2px';
    $styles = $autosize ? array_filter([
        "min-height: calc({$rows}lh + {$edge})",
        $maxRows ? "max-height: calc({$maxRows}lh + {$edge})" : null,
    ]) : [];

    $textarea = $attributes
        ->except(['aria-describedby', 'value'])
        ->merge(array_merge($control['attributes'], array_filter([
            'id' => $id,
            'rows' => $rows,
            'aria-describedby' => \JayAitch\GiraffeUI\Support\Form::describedBy($control['attributes']['aria-describedby'] ?? null, $counter && $max ? "{$id}-limit" : null),
        ])))
        ->class([
            'block w-full min-w-0 resize-y rounded-gui border border-gui-input bg-white px-3 py-2 text-sm text-zinc-900 shadow-xs transition-colors',
            'placeholder:text-zinc-500 dark:placeholder:text-zinc-400',
            'focus:outline-2 focus:-outline-offset-1 focus:outline-gui-primary-text',
            'aria-invalid:border-gui-danger-text aria-invalid:focus:outline-gui-danger-text',
            'disabled:cursor-not-allowed disabled:bg-zinc-100 disabled:text-zinc-500 dark:disabled:bg-zinc-800 dark:disabled:text-zinc-400',
            'dark:bg-zinc-900 dark:text-zinc-100 dark:scheme-dark',
            'field-sizing-content supports-[field-sizing:content]:resize-none' => $autosize,
        ])
        ->when($styles, fn ($bag) => $bag->style($styles));
@endphp

@if ($counter)
    {{--
        The visible count is hidden from screen readers, which would hear it on every keystroke. Instead the limit is
        part of the description, and a polite live region reports what's left once typing pauses (as GOV.UK does).
    --}}
    <div
        x-data="{
            length: {{ $length }},
            max: {{ $max ?? 'null' }},
            announcement: '',
            timer: null,

            init() {
                const el = this.$refs.textarea;
                const value = Object.getOwnPropertyDescriptor(HTMLTextAreaElement.prototype, 'value');

                // wire:model, x-model and scripts set the value without an input event, so count those too.
                Object.defineProperty(el, 'value', {
                    configurable: true,
                    get: () => value.get.call(el),
                    set: (text) => {
                        value.set.call(el, text);
                        this.length = el.value.length;
                    },
                });
                el.form?.addEventListener('reset', () => setTimeout(() => this.length = el.value.length));
                this.length = el.value.length;
            },
            count(el) {
                this.length = el.value.length;
                clearTimeout(this.timer);
                this.timer = setTimeout(() => this.announcement = this.describe(), 1000);
            },
            describe() {
                if (this.max === null) {
                    return this.length === 1 ? '1 character' : this.length + ' characters';
                }
                const left = this.max - this.length;
                return left === 1 ? '1 character left' : left + ' characters left';
            },
        }"
        class="space-y-1.5"
    >
        <textarea x-ref="textarea" x-on:input="count($el)" {{ $textarea }}>{{ $value }}</textarea>

        <div class="flex justify-end text-xs text-zinc-600 tabular-nums dark:text-zinc-400">
            @if ($max)
                <span id="{{ $id }}-limit" class="sr-only">Up to {{ $max }} characters.</span>
            @endif
            <span aria-hidden="true" x-bind:class="max !== null && length >= max && 'font-medium text-gui-danger-text'">
                <span x-text="length">{{ $length }}</span>{{ $max ? " / {$max}" : ' characters' }}
            </span>
            <span class="sr-only" aria-live="polite" x-text="announcement"></span>
        </div>
    </div>
@else
    <textarea {{ $textarea }}>{{ $value }}</textarea>
@endif
