@aware(['guiField' => null])

@props([
    'size' => 'md',
    'invalid' => null,
    'locale' => null,
    'weekStart' => null,
    'buttonLabel' => 'Choose date',
    'previousLabel' => 'Previous month',
    'nextLabel' => 'Next month',
    'disabledDates' => [],
    'disabledWeekdays' => [],
    'unavailableMessage' => "That date isn't available.",
])

@php
    $control = \JayAitch\GiraffeUI\Support\Form::control($guiField, $attributes, $errors ?? null, $invalid);
    $id = $control['attributes']['id'] ?? \JayAitch\GiraffeUI\Support\Id::make('gui-date');
    $value = \JayAitch\GiraffeUI\Support\Form::value($attributes, $control['errorKey'], 'date');
    $value = $value instanceof \DateTimeInterface ? $value->format('Y-m-d') : $value;
    $disabled = $attributes->has('disabled') && $attributes->get('disabled') !== false;
    $anchor = \JayAitch\GiraffeUI\Support\Anchor::name($id);

    // Dates and weekdays (0 Sunday to 6 Saturday) that can't be picked, on top of min and max.
    $blockedDates = collect($disabledDates)->map(fn ($date) => $date instanceof \DateTimeInterface ? $date->format('Y-m-d') : (string) $date)->values()->all();
    $blockedWeekdays = collect($disabledWeekdays)->map(fn ($day) => ((int) $day) % 7)->unique()->values()->all();

    // Firefox can't hide its own picker icon, so once Alpine is running the right padding shrinks to centre that
    // icon under our button, which covers it. Chromium and Safari hide theirs instead.
    $sizes = [
        'sm' => 'h-8 pl-2.5 pr-9 text-sm data-enhanced:supports-[-moz-appearance:none]:pr-2',
        'md' => 'h-10 pl-3 pr-10 text-sm data-enhanced:supports-[-moz-appearance:none]:pr-3',
        'lg' => 'h-12 pl-3.5 pr-11 text-base data-enhanced:supports-[-moz-appearance:none]:pr-4',
    ];
@endphp

{{--
    The control is a native date input: it works without JavaScript, types and validates like any date field, and
    submits Y-m-d. Alpine adds a button that opens a calendar (the WAI-ARIA date picker dialog pattern) in a popover.
    Disabled dates and weekdays can't be picked in the calendar, and a typed one fails the browser's validation;
    validate them on the server too.
--}}
<div
    class="relative"
    x-data="{
        locale: @js($locale ? str_replace('_', '-', $locale) : null),
        weekStart: @js($weekStart === null ? null : ((int) $weekStart) % 7),
        selected: '',
        focused: '',
        blockedDates: @js($blockedDates),
        blockedWeekdays: @js($blockedWeekdays),

        init() {
            // A typed date that can't be picked fails validation, like one outside min and max.
            // (The browser already reports dates outside min and max.)
            const check = () => this.$refs.input.setCustomValidity(this.blocked(this.parse(this.$refs.input.value)) ? @js($unavailableMessage) : '');
            this.$refs.input.addEventListener('input', check);
            this.$refs.input.addEventListener('change', check);
            check();

            this.locale ??= document.documentElement.lang || navigator.language;
            if (this.weekStart === null) {
                try {
                    const locale = new Intl.Locale(this.locale);
                    this.weekStart = (locale.getWeekInfo?.() ?? locale.weekInfo)?.firstDay % 7;
                } catch {}
                this.weekStart ??= 1;
                if (Number.isNaN(this.weekStart)) this.weekStart = 1;
            }
        },

        // Dates are midnight UTC, so adding days never trips over daylight saving.
        parse(iso) {
            const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(iso ?? '');
            return match ? new Date(Date.UTC(+match[1], match[2] - 1, +match[3])) : null;
        },
        iso(date) {
            return date.toISOString().slice(0, 10);
        },
        today() {
            const now = new Date();
            return new Date(Date.UTC(now.getFullYear(), now.getMonth(), now.getDate()));
        },
        format(date, options) {
            return new Intl.DateTimeFormat(this.locale, { timeZone: 'UTC', ...options }).format(date);
        },
        outside(date) {
            const min = this.parse(this.$refs.input.min);
            const max = this.parse(this.$refs.input.max);
            return (min && date < min) || (max && date > max);
        },
        // A disabled date or weekday.
        blocked(date) {
            return !! date && (this.blockedDates.includes(this.iso(date)) || this.blockedWeekdays.includes(date.getUTCDay()));
        },
        unavailable(date) {
            return !! date && (this.outside(date) || this.blocked(date));
        },
        clamp(date) {
            const min = this.parse(this.$refs.input.min);
            const max = this.parse(this.$refs.input.max);
            return min && date < min ? min : max && date > max ? max : date;
        },

        get current() {
            return this.parse(this.focused) ?? this.today();
        },
        get title() {
            return this.format(this.current, { month: 'long', year: 'numeric' });
        },
        get weekdays() {
            // 1 January 2023 was a Sunday.
            return [0, 1, 2, 3, 4, 5, 6].map((i) => {
                const date = new Date(Date.UTC(2023, 0, 1 + ((this.weekStart + i) % 7)));
                return { short: this.format(date, { weekday: 'short' }), long: this.format(date, { weekday: 'long' }) };
            });
        },
        get weeks() {
            const year = this.current.getUTCFullYear();
            const month = this.current.getUTCMonth();
            const offset = (new Date(Date.UTC(year, month, 1)).getUTCDay() - this.weekStart + 7) % 7;
            const days = new Date(Date.UTC(year, month + 1, 0)).getUTCDate();
            const today = this.iso(this.today());

            const cells = Array.from({ length: offset }, (_, i) => ({ key: 'before-' + i }));
            for (let day = 1; day <= days; day++) {
                const date = new Date(Date.UTC(year, month, day));
                const iso = this.iso(date);
                cells.push({ key: iso, iso, day, label: this.format(date, { dateStyle: 'full' }), today: iso === today, disabled: this.unavailable(date) });
            }
            while (cells.length % 7) cells.push({ key: 'after-' + cells.length });

            return Array.from({ length: cells.length / 7 }, (_, i) => cells.slice(i * 7, i * 7 + 7));
        },

        toggled(event) {
            if (event.newState !== 'open') return;
            this.selected = this.$refs.input.value;
            this.focused = this.iso(this.clamp(this.parse(this.selected) ?? this.today()));
            this.$nextTick(() => this.focusDay());
        },
        // Focus straight away when the day is already showing, so fast typing never acts on a stale day.
        focusDay() {
            const day = () => this.$refs.grid.querySelector(`[data-date='${this.focused}']`);
            day() ? day().focus() : this.$nextTick(() => day()?.focus());
        },
        // Move the focus by days or months, keeping it inside min and max.
        move(days, months = 0) {
            let date = this.current;
            if (months) {
                const target = new Date(Date.UTC(date.getUTCFullYear(), date.getUTCMonth() + months, 1));
                const last = new Date(Date.UTC(target.getUTCFullYear(), target.getUTCMonth() + 1, 0)).getUTCDate();
                date = new Date(Date.UTC(target.getUTCFullYear(), target.getUTCMonth(), Math.min(date.getUTCDate(), last)));
            }
            date = new Date(date.getTime() + days * 86400000);
            this.focused = this.iso(this.clamp(date));
        },
        key(event) {
            const weekday = (this.current.getUTCDay() - this.weekStart + 7) % 7;
            const keys = {
                ArrowLeft: () => this.move(-1),
                ArrowRight: () => this.move(1),
                ArrowUp: () => this.move(-7),
                ArrowDown: () => this.move(7),
                Home: () => this.move(-weekday),
                End: () => this.move(6 - weekday),
                PageUp: () => this.move(0, event.shiftKey ? -12 : -1),
                PageDown: () => this.move(0, event.shiftKey ? 12 : 1),
            };
            // Enter and Space pick the focused date from state rather than whichever button has focus.
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                return this.choose(this.focused);
            }
            if (! keys[event.key]) return;
            event.preventDefault();
            keys[event.key]();
            this.focusDay();
        },
        choose(iso) {
            const date = this.parse(iso);
            if (! date || this.unavailable(date)) return;
            const input = this.$refs.input;
            input.value = iso;
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
            this.$refs.popup.hidePopover();
            this.$refs.trigger.focus();
        },
    }"
>
    <input
        type="date"
        x-ref="input"
        x-bind:data-enhanced="true"
        {{ $attributes
            ->except(['aria-describedby', 'value', 'id'])
            ->merge($control['attributes'] + ['id' => $id] + ($value !== null ? ['value' => $value] : []))
            ->class([
                'block w-full min-w-0 rounded-gui border border-gui-input bg-white text-zinc-900 shadow-xs transition-colors',
                'focus:outline-2 focus:-outline-offset-1 focus:outline-gui-primary-text',
                'aria-invalid:border-gui-danger-text aria-invalid:focus:outline-gui-danger-text',
                'disabled:cursor-not-allowed disabled:bg-zinc-100 disabled:text-zinc-500 dark:disabled:bg-zinc-800 dark:disabled:text-zinc-400',
                'dark:bg-zinc-900 dark:text-zinc-100 dark:scheme-dark',
                // Our button replaces the browser's picker icon, but only once Alpine is running (see $sizes for Firefox).
                'data-enhanced:[&::-webkit-calendar-picker-indicator]:hidden',
                $sizes[$size] ?? $sizes['md'],
            ])
            ->style("anchor-name: {$anchor}") }}
    >

    {{--
        Pinned to the input with anchor positioning, so width classes on the input move it too.
        Its background matches the input, so it covers Firefox's own picker icon.
    --}}
    <button
        type="button"
        x-ref="trigger"
        style="{{ \JayAitch\GiraffeUI\Support\Anchor::pin($id, 'right', '0.25rem') }}"
        x-cloak
        commandfor="{{ $id }}-calendar"
        command="toggle-popover"
        aria-label="{{ $buttonLabel }}"
        aria-haspopup="dialog"
        @disabled($disabled)
        class="absolute inset-y-1 right-1 flex aspect-square items-center justify-center rounded-[calc(var(--radius-gui)-2px)] bg-white text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-gui-primary-text disabled:pointer-events-none disabled:bg-zinc-100 dark:bg-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100 dark:disabled:bg-zinc-800"
    >
        <x-gui::icon name="calendar" size="sm" />
    </button>

    <div
        id="{{ $id }}-calendar"
        x-ref="popup"
        popover
        role="dialog"
        aria-labelledby="{{ $id }}-month"
        style="position-anchor: {{ $anchor }}"
        x-on:toggle="toggled($event)"
        x-on:focusout="if ($event.relatedTarget && ! $el.contains($event.relatedTarget) && $event.relatedTarget !== $refs.trigger) $el.hidePopover()"
        class="{{ \JayAitch\GiraffeUI\Support\Anchor::position('bottom-end') }} inset-auto m-0 w-max rounded-gui border border-zinc-200 bg-white p-3 text-sm text-zinc-900 shadow-lg dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-100"
    >
        <div class="mb-2 flex items-center justify-between gap-2">
            <button type="button" aria-label="{{ $previousLabel }}" x-on:click="move(0, -1)" class="flex size-8 items-center justify-center rounded-[calc(var(--radius-gui)-2px)] text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-gui-primary-text dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100">
                <x-gui::icon name="chevron-left" size="sm" />
            </button>
            <h2 id="{{ $id }}-month" class="font-semibold" aria-live="polite" x-text="title"></h2>
            <button type="button" aria-label="{{ $nextLabel }}" x-on:click="move(0, 1)" class="flex size-8 items-center justify-center rounded-[calc(var(--radius-gui)-2px)] text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-gui-primary-text dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100">
                <x-gui::icon name="chevron-right" size="sm" />
            </button>
        </div>

        <table role="grid" aria-labelledby="{{ $id }}-month" x-ref="grid" x-on:keydown="key($event)" class="border-collapse">
            <thead>
                <tr>
                    <template x-for="weekday in weekdays" x-bind:key="weekday.long">
                        <th scope="col" x-bind:abbr="weekday.long" x-text="weekday.short" class="size-9 text-xs font-medium text-zinc-600 dark:text-zinc-400"></th>
                    </template>
                </tr>
            </thead>
            <tbody>
                <template x-for="(week, index) in weeks" x-bind:key="index">
                    <tr>
                        <template x-for="cell in week" x-bind:key="cell.key">
                            <td class="p-0 text-center" x-bind:aria-selected="cell.iso ? String(cell.iso === selected) : null">
                                <template x-if="cell.iso">
                                    <button
                                        type="button"
                                        x-bind:data-date="cell.iso"
                                        x-bind:tabindex="cell.iso === focused ? 0 : -1"
                                        x-bind:aria-label="cell.label"
                                        x-bind:aria-current="cell.today ? 'date' : null"
                                        x-bind:aria-disabled="cell.disabled ? 'true' : null"
                                        x-bind:data-selected="cell.iso === selected"
                                        x-on:mousedown="cell.disabled && $event.preventDefault()"
                                        x-on:click="choose(cell.iso)"
                                        x-text="cell.day"
                                        class="size-9 rounded-[calc(var(--radius-gui)-2px)] tabular-nums transition-colors hover:bg-zinc-100 focus-visible:outline-2 focus-visible:outline-gui-primary-text aria-[current=date]:font-semibold aria-[current=date]:text-gui-primary-text aria-disabled:cursor-not-allowed aria-disabled:text-zinc-400 aria-disabled:line-through aria-disabled:hover:bg-transparent data-[selected=true]:bg-gui-primary data-[selected=true]:text-gui-primary-foreground data-[selected=true]:hover:bg-gui-primary/90 dark:hover:bg-zinc-800 dark:aria-disabled:text-zinc-500"
                                    ></button>
                                </template>
                            </td>
                        </template>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>
