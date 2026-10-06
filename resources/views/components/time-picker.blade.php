@aware(['guiField' => null])

@props([
    'size' => 'md',
    'invalid' => null,
    'interval' => 30,
    'locale' => null,
    'buttonLabel' => null,
])

@php
    $buttonLabel ??= __('giraffeui::ui.choose_time');

    $control = \JayAitch\GiraffeUI\Support\Form::control($guiField, $attributes, $errors ?? null, $invalid);
    $id = $control['attributes']['id'] ?? \JayAitch\GiraffeUI\Support\Id::make('gui-time');
    $disabled = $attributes->has('disabled') && $attributes->get('disabled') !== false;
    $readonly = $attributes->has('readonly') && $attributes->get('readonly') !== false;
    $anchor = \JayAitch\GiraffeUI\Support\Anchor::name($id);
    $time = fn ($value) => $value instanceof \DateTimeInterface ? $value->format('H:i') : $value;
    $value = $time(\JayAitch\GiraffeUI\Support\Form::value($attributes, $control['errorKey'], 'time'));
    $interval = max(1, min(720, (int) $interval));

    // Every class is written out in full so Tailwind can find it when scanning this file.
    $sizes = [
        'sm' => 'h-8 ps-2.5 pe-9 text-sm',
        'md' => 'h-10 ps-3 pe-10 text-sm',
        'lg' => 'h-12 ps-3.5 pe-11 text-base',
    ];
@endphp

{{--
    The control is a native time input: it works without JavaScript, types and validates like any time field (min,
    max and step included), and submits H:i. Alpine adds a button that opens a list of times every interval minutes,
    between min and max, written the way the visitor's locale writes them (2:30 PM, or 14:30).
--}}
<div
    class="relative"
    x-data="{
        locale: @js($locale ? str_replace('_', '-', $locale) : null),
        interval: {{ $interval }},
        times: [],
        active: -1,
        value: '',

        init() {
            const input = this.$refs.input;
            // The chosen time, kept in step however it changes: typed, picked or set by Livewire.
            const sync = () => this.value = input.value.slice(0, 5);
            input.addEventListener('input', sync);
            input.addEventListener('change', sync);
            sync();
            const minutes = (value) => value ? Number(value.slice(0, 2)) * 60 + Number(value.slice(3, 5)) : null;
            const from = minutes(input.min) ?? 0;
            const to = minutes(input.max) ?? 24 * 60 - 1;
            const format = new Intl.DateTimeFormat(this.locale ?? undefined, { hour: 'numeric', minute: '2-digit', timeZone: 'UTC' });
            // Start on the first whole interval at or after min, so 09:10 with 30 minutes lists 09:30, 10:00, …
            for (let at = Math.ceil(from / this.interval) * this.interval; at <= to && at < 24 * 60; at += this.interval) {
                const value = String(Math.floor(at / 60)).padStart(2, '0') + ':' + String(at % 60).padStart(2, '0');
                this.times.push({ value, label: format.format(new Date(Date.UTC(2000, 0, 1, Math.floor(at / 60), at % 60))) });
            }
        },
        // On opening: the chosen time, or the next one in the list after it, scrolled into the middle.
        toggled(event) {
            if (event.newState !== 'open') return;
            const index = this.times.findIndex((time) => time.value >= this.value);
            this.active = this.value ? (index === -1 ? this.times.length - 1 : index) : 0;
            this.$nextTick(() => {
                this.$refs.list.focus();
                this.scroll();
            });
        },
        scroll() {
            this.$refs.list.querySelector('[data-active=true]')?.scrollIntoView({ block: 'nearest' });
        },
        choose(index) {
            const input = this.$refs.input;
            input.value = this.times[index].value;
            // Tell the browser, Livewire and Alpine, as if it had been typed.
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
            this.$refs.popup.hidePopover();
            input.focus();
        },
        key(event) {
            const last = this.times.length - 1;
            const page = Math.max(1, Math.round(120 / this.interval));
            const moves = { ArrowDown: 1, ArrowUp: -1, PageDown: page, PageUp: -page };
            if (event.key in moves) this.active = Math.max(0, Math.min(last, this.active + moves[event.key]));
            else if (event.key === 'Home') this.active = 0;
            else if (event.key === 'End') this.active = last;
            else if (event.key === 'Enter' || event.key === ' ') return (event.preventDefault(), this.active >= 0 && this.choose(this.active));
            else if (event.key === 'Tab') return this.$refs.popup.hidePopover();
            else return;
            event.preventDefault();
            this.$nextTick(() => this.scroll());
        },
    }"
>
    <input
        type="time"
        x-ref="input"
        x-bind:data-enhanced="true"
        {{ $attributes
            ->except(['aria-describedby', 'value', 'id'])
            ->merge($control['attributes'] + ['id' => $id] + ($value !== null ? ['value' => $value] : []))
            ->class([
                'block w-full min-w-0 rounded-gui border border-gui-input bg-white text-zinc-900 tabular-nums shadow-xs transition-colors',
                'focus:outline-2 focus:-outline-offset-1 focus:outline-gui-primary-text',
                'aria-invalid:border-gui-danger-text aria-invalid:focus:outline-gui-danger-text',
                'disabled:cursor-not-allowed disabled:bg-zinc-100 disabled:text-zinc-500 dark:disabled:bg-zinc-800 dark:disabled:text-zinc-400',
                'dark:bg-zinc-900 dark:text-zinc-100 dark:scheme-dark',
                // Our button replaces Chromium's clock icon once Alpine is running. Firefox and Safari don't show one.
                'data-enhanced:[&::-webkit-calendar-picker-indicator]:hidden',
                $sizes[$size] ?? $sizes['md'],
            ])
            ->style("anchor-name: {$anchor}") }}
    >

    {{-- Pinned to the input with anchor positioning, so width classes on the input move it too. --}}
    <button
        type="button"
        x-ref="trigger"
        style="{{ \JayAitch\GiraffeUI\Support\Anchor::pin($id, 'right', '0.25rem') }}"
        x-cloak
        commandfor="{{ $id }}-times"
        command="toggle-popover"
        aria-label="{{ $buttonLabel }}"
        aria-haspopup="listbox"
        @disabled($disabled || $readonly)
        class="absolute inset-y-1 end-1 flex aspect-square items-center justify-center rounded-[calc(var(--radius-gui)-2px)] text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-gui-primary-text disabled:pointer-events-none dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100"
    >
        <x-gui::icon name="clock" size="sm" />
    </button>

    <div
        id="{{ $id }}-times"
        x-ref="popup"
        popover
        style="position-anchor: {{ $anchor }}"
        x-on:toggle="toggled($event)"
        x-on:focusout="if ($event.relatedTarget && ! $el.contains($event.relatedTarget) && $event.relatedTarget !== $refs.trigger) $el.hidePopover()"
        class="{{ \JayAitch\GiraffeUI\Support\Anchor::position('bottom-end') }} inset-auto m-0 w-max min-w-32 rounded-gui border border-zinc-200 bg-white p-1 text-sm text-zinc-900 shadow-lg dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-100"
    >
        <ul
            x-ref="list"
            role="listbox"
            tabindex="0"
            aria-label="{{ $buttonLabel }}"
            x-bind:aria-activedescendant="active >= 0 ? @js($id.'-time-') + active : null"
            x-on:keydown="key($event)"
            class="max-h-60 overflow-y-auto overscroll-contain focus:outline-none"
        >
            <template x-for="(time, index) in times" x-bind:key="time.value">
                <li
                    role="option"
                    x-bind:id="@js($id.'-time-') + index"
                    x-bind:aria-selected="String(time.value === value)"
                    x-bind:data-active="index === active"
                    x-on:mousemove="active = index"
                    x-on:click="choose(index)"
                    x-text="time.label"
                    class="cursor-pointer rounded-[calc(var(--radius-gui)-2px)] px-3 py-1.5 tabular-nums data-[active=true]:bg-zinc-100 aria-selected:font-semibold aria-selected:text-gui-primary-text dark:data-[active=true]:bg-zinc-800"
                ></li>
            </template>
        </ul>
    </div>
</div>
