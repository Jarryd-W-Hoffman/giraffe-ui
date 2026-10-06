@aware(['guiField' => null])

@props([
    'size' => 'md',
    'invalid' => null,
    'locale' => null,
    'weekStart' => null,
    'buttonLabel' => null,
    'previousLabel' => null,
    'nextLabel' => null,
    'disabledDates' => [],
    'disabledWeekdays' => [],
    'unavailableMessage' => null,
    'range' => false,
    'startLabel' => null,
    'endLabel' => null,
])

@php
    $previousLabel ??= __('giraffeui::ui.previous_month');
    $nextLabel ??= __('giraffeui::ui.next_month');
    $startLabel ??= __('giraffeui::ui.start_date');
    $endLabel ??= __('giraffeui::ui.end_date');
    $unavailableMessage ??= __('giraffeui::ui.date_unavailable');

    $control = \JayAitch\GiraffeUI\Support\Form::control($guiField, $attributes, $errors ?? null, $invalid);
    $id = $control['attributes']['id'] ?? \JayAitch\GiraffeUI\Support\Id::make('gui-date');
    $disabled = $attributes->has('disabled') && $attributes->get('disabled') !== false;
    $anchor = \JayAitch\GiraffeUI\Support\Anchor::name($id);
    $buttonLabel ??= __($range ? 'giraffeui::ui.choose_dates' : 'giraffeui::ui.choose_date');
    $date = fn ($value) => $value instanceof \DateTimeInterface ? $value->format('Y-m-d') : $value;

    // Dates and weekdays (0 Sunday to 6 Saturday) that can't be picked, on top of min and max.
    $blockedDates = collect($disabledDates)->map(fn ($day) => (string) $date($day))->values()->all();
    $blockedWeekdays = collect($disabledWeekdays)->map(fn ($day) => ((int) $day) % 7)->unique()->values()->all();

    if ($range) {
        // A range is two native date inputs under one name: name[start] and name[end], with errors and old input
        // at name.start and name.end. wire:model="stay" or x-model="stay" binds stay.start and stay.end.
        $base = $control['name'];
        $value = (array) ($attributes->get('value') ?? []);
        $parts = [];

        foreach (['start', 'end'] as $part) {
            $errorKey = $base ? \JayAitch\GiraffeUI\Support\Form::errorKey($base).'.'.$part : null;
            $binding = $attributes->whereStartsWith(['wire:model', 'x-model']);
            $message = \JayAitch\GiraffeUI\Support\Form::error($errorKey, null, $errors ?? null);

            $parts[$part] = [
                'id' => $part === 'start' ? $id : "{$id}-end",
                'name' => $base ? "{$base}[{$part}]" : null,
                'value' => $date($value[$part] ?? ($binding->isEmpty() && $errorKey ? old($errorKey) : null)),
                'label' => $part === 'start' ? $startLabel : $endLabel,
                'binding' => collect($binding->getAttributes())->mapWithKeys(fn ($model, $key) => [$key => "{$model}.{$part}"])->all(),
                'message' => $message,
                'invalid' => $control['invalid'] || $message !== null,
            ];
        }

        // min, max, required and the like apply to both dates; class and anything else go on the control.
        $shared = $attributes->only(['min', 'max', 'required', 'disabled', 'readonly', 'form', 'autocomplete'])
            ->merge(isset($control['attributes']['required']) ? ['required' => true] : []);
        $outer = $attributes->except(array_merge(array_keys($shared->getAttributes()), ['value', 'name', 'id', 'aria-describedby'], array_keys($attributes->whereStartsWith(['wire:model', 'x-model'])->getAttributes())));
        $describedBy = $control['attributes']['aria-describedby'] ?? null;
    } else {
        $value = $date(\JayAitch\GiraffeUI\Support\Form::value($attributes, $control['errorKey'], 'date'));
    }

    // Every class is written out in full so Tailwind can find it when scanning this file.
    // Firefox can't hide its own picker icon, so once Alpine is running the right padding shrinks to centre that
    // icon under our button, which covers it. Chromium and Safari hide theirs instead.
    $sizes = [
        'sm' => 'h-8 ps-2.5 pe-9 text-sm data-enhanced:supports-[-moz-appearance:none]:pe-2',
        'md' => 'h-10 ps-3 pe-10 text-sm data-enhanced:supports-[-moz-appearance:none]:pe-3',
        'lg' => 'h-12 ps-3.5 pe-11 text-base data-enhanced:supports-[-moz-appearance:none]:pe-4',
    ];
    $rangeSizes = [
        'sm' => 'h-8 gap-1.5 ps-2.5 pe-1 text-sm',
        'md' => 'h-10 gap-2 ps-3 pe-1 text-sm',
        'lg' => 'h-12 gap-2 ps-3.5 pe-1 text-base',
    ];
    $buttonSizes = ['sm' => 'size-6', 'md' => 'size-8', 'lg' => 'size-10'];
@endphp

{{--
    The control is a native date input: it works without JavaScript, types and validates like any date field, and
    submits Y-m-d. Alpine adds a button that opens a calendar (the WAI-ARIA date picker dialog pattern) in a popover.
    Disabled dates and weekdays can't be picked in the calendar, and a typed one fails the browser's validation;
    validate them on the server too. With range, there are two inputs and the calendar picks a start, then an end.
--}}
<div
    class="relative"
    x-data="{
        locale: @js($locale ? str_replace('_', '-', $locale) : null),
        weekStart: @js($weekStart === null ? null : ((int) $weekStart) % 7),
        range: @js((bool) $range),
        selected: '',
        start: '',
        end: '',
        // In a range, whether the next pick is the start or the end, and the day under the pointer for a preview.
        picking: 'start',
        hovered: '',
        focused: '',
        blockedDates: @js($blockedDates),
        blockedWeekdays: @js($blockedWeekdays),

        init() {
            // A typed date that can't be picked fails validation. (The browser already reports dates outside min and max.)
            for (const input of this.inputs()) {
                const check = () => input.setCustomValidity(this.blocked(this.parse(input.value)) ? @js($unavailableMessage) : '');
                input.addEventListener('input', check);
                input.addEventListener('change', check);
                check();
            }
            // The end can't be before the start, so the browser checks that too.
            if (this.range) {
                const floor = this.$refs.end.getAttribute('min') ?? '';
                const follow = () => this.$refs.end.min = this.$refs.input.value && this.$refs.input.value > floor ? this.$refs.input.value : floor;
                this.$refs.input.addEventListener('input', follow);
                this.$refs.input.addEventListener('change', follow);
                follow();
            }

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
        inputs() {
            return this.range ? [this.$refs.input, this.$refs.end] : [this.$refs.input];
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

        // A day is selected when it's the value, or in a range, the start or the end.
        isSelected(iso) {
            return this.range ? iso === this.start || iso === this.end : iso === this.selected;
        },
        // Days between the start and the end, or while picking the end, up to the day being pointed at or focused.
        inRange(iso) {
            if (! this.range || ! this.start) return false;
            const last = this.picking === 'end' ? (this.hovered || this.focused) : this.end;
            return !! last && iso > this.start && iso < last;
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
            if (this.range) {
                this.start = this.$refs.input.value;
                this.end = this.$refs.end.value;
                this.picking = 'start';
                this.hovered = '';
            }
            this.focused = this.iso(this.clamp(this.parse(this.$refs.input.value) ?? this.today()));
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
            const back = this.$refs.grid.matches(':dir(rtl)') ? 1 : -1;
            const keys = {
                ArrowLeft: () => this.move(back),
                ArrowRight: () => this.move(-back),
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
            this.hovered = '';
            keys[event.key]();
            this.focusDay();
        },
        // Set an input's value the way typing would, so wire:model, x-model and validation follow.
        write(input, iso) {
            input.value = iso;
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
        },
        choose(iso) {
            const date = this.parse(iso);
            if (! date || this.unavailable(date)) return;

            if (this.range) {
                // The first pick is the start. A second pick before it starts again from there.
                if (this.picking === 'start' || iso < this.start) {
                    this.start = iso;
                    this.write(this.$refs.input, iso);
                    if (this.end && this.end < iso) {
                        this.end = '';
                        this.write(this.$refs.end, '');
                    }
                    this.picking = 'end';
                    this.focused = iso;
                    return this.focusDay();
                }
                this.end = iso;
                this.write(this.$refs.end, iso);
            } else {
                this.write(this.$refs.input, iso);
            }

            this.$refs.popup.hidePopover();
            this.$refs.trigger.focus();
        },
    }"
>
    @if ($range)
        {{--
            One control holding both inputs. Each input is wider than its box by Firefox's icon, so the box clips that
            icon off (Firefox can't hide it); Chromium and Safari hide theirs.
        --}}
        <div
            role="group"
            @if ($guiField && ($guiField['label'] ?? false)) aria-labelledby="{{ $id }}-label" @endif
            style="anchor-name: {{ $anchor }}"
            {{ $outer->class([
                'flex w-full min-w-0 items-center rounded-gui border border-gui-input bg-white text-zinc-900 shadow-xs transition-colors',
                'has-focus-visible:outline-2 has-focus-visible:-outline-offset-1 has-focus-visible:outline-gui-primary-text',
                'has-aria-invalid:border-gui-danger-text',
                'has-disabled:cursor-not-allowed has-disabled:bg-zinc-100 has-disabled:text-zinc-500 dark:has-disabled:bg-zinc-800 dark:has-disabled:text-zinc-400',
                'dark:bg-zinc-900 dark:text-zinc-100',
                $rangeSizes[$size] ?? $rangeSizes['md'],
            ]) }}
        >
            @foreach ($parts as $part => $input)
                @if ($part === 'end')
                    <span class="text-zinc-500 dark:text-zinc-400" aria-hidden="true">–</span>
                @endif
                {{-- Full height, so each date is a comfortable target, not just its line of text. --}}
                <span class="flex min-w-0 flex-1 self-stretch overflow-hidden">
                    <input
                        type="date"
                        @if ($part === 'start') x-ref="input" @else x-ref="end" @endif
                        x-bind:data-enhanced="true"
                        id="{{ $input['id'] }}"
                        @if ($input['name']) name="{{ $input['name'] }}" @endif
                        @if ($input['value']) value="{{ $input['value'] }}" @endif
                        aria-label="{{ $input['label'] }}"
                        @if ($input['invalid']) aria-invalid="true" @endif
                        @if ($d = \JayAitch\GiraffeUI\Support\Form::describedBy($describedBy, $input['message'] ? "{$input['id']}-error" : null)) aria-describedby="{{ $d }}" @endif
                        {{ $shared->merge($input['binding'])->class([
                            'h-full w-full flex-none border-0 bg-transparent p-0 text-inherit focus:outline-none disabled:cursor-not-allowed dark:scheme-dark',
                            'data-enhanced:[&::-webkit-calendar-picker-indicator]:hidden data-enhanced:supports-[-moz-appearance:none]:w-[calc(100%+1.75rem)]',
                        ]) }}
                    >
                </span>
            @endforeach

            <button
                type="button"
                x-ref="trigger"
                x-cloak
                commandfor="{{ $id }}-calendar"
                command="toggle-popover"
                aria-label="{{ $buttonLabel }}"
                aria-haspopup="dialog"
                @disabled($disabled)
                class="flex {{ $buttonSizes[$size] ?? $buttonSizes['md'] }} shrink-0 items-center justify-center rounded-[calc(var(--radius-gui)-2px)] text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-gui-primary-text disabled:pointer-events-none dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100"
            >
                <x-gui::icon name="calendar" size="sm" />
            </button>
        </div>

        @foreach ($parts as $input)
            @if ($input['message'])
                <p id="{{ $input['id'] }}-error" class="mt-1.5 text-sm font-medium text-gui-danger-text">{{ $input['message'] }}</p>
            @endif
        @endforeach
    @else
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
            class="absolute inset-y-1 end-1 flex aspect-square items-center justify-center rounded-[calc(var(--radius-gui)-2px)] bg-white text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-gui-primary-text disabled:pointer-events-none disabled:bg-zinc-100 dark:bg-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100 dark:disabled:bg-zinc-800"
        >
            <x-gui::icon name="calendar" size="sm" />
        </button>
    @endif

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
                <x-gui::icon name="chevron-left" size="sm" class="rtl:rotate-180" />
            </button>
            <h2 id="{{ $id }}-month" class="font-semibold" aria-live="polite" x-text="title"></h2>
            <button type="button" aria-label="{{ $nextLabel }}" x-on:click="move(0, 1)" class="flex size-8 items-center justify-center rounded-[calc(var(--radius-gui)-2px)] text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-gui-primary-text dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100">
                <x-gui::icon name="chevron-right" size="sm" class="rtl:rotate-180" />
            </button>
        </div>

        @if ($range)
            {{-- Says which end the next pick sets, for everyone and for screen readers. --}}
            <p class="mb-2 text-center text-xs text-zinc-600 dark:text-zinc-400" aria-live="polite" x-text="picking === 'start' ? @js(__('giraffeui::ui.pick_start')) : @js(__('giraffeui::ui.pick_end'))"></p>
        @endif

        <table role="grid" aria-labelledby="{{ $id }}-month" x-ref="grid" x-on:keydown="key($event)" x-on:mouseleave="hovered = ''" class="border-collapse">
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
                            <td class="p-0 text-center" x-bind:aria-selected="cell.iso ? String(isSelected(cell.iso) || inRange(cell.iso)) : null">
                                <template x-if="cell.iso">
                                    <button
                                        type="button"
                                        x-bind:data-date="cell.iso"
                                        x-bind:tabindex="cell.iso === focused ? 0 : -1"
                                        x-bind:aria-label="cell.label"
                                        x-bind:aria-current="cell.today ? 'date' : null"
                                        x-bind:aria-disabled="cell.disabled ? 'true' : null"
                                        x-bind:data-selected="isSelected(cell.iso)"
                                        x-bind:data-in-range="inRange(cell.iso)"
                                        x-on:mousedown="cell.disabled && $event.preventDefault()"
                                        x-on:mouseenter="hovered = cell.disabled ? '' : cell.iso"
                                        x-on:click="choose(cell.iso)"
                                        x-text="cell.day"
                                        class="size-9 rounded-[calc(var(--radius-gui)-2px)] tabular-nums transition-colors hover:bg-zinc-100 focus-visible:outline-2 focus-visible:outline-gui-primary-text aria-[current=date]:font-semibold aria-[current=date]:text-gui-primary-text aria-disabled:cursor-not-allowed aria-disabled:text-zinc-400 aria-disabled:line-through aria-disabled:hover:bg-transparent data-[in-range=true]:rounded-none data-[in-range=true]:bg-gui-primary/10 data-[selected=true]:bg-gui-primary data-[selected=true]:text-gui-primary-foreground data-[selected=true]:hover:bg-gui-primary/90 dark:hover:bg-zinc-800 dark:aria-disabled:text-zinc-500 dark:data-[in-range=true]:bg-gui-primary/25"
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
