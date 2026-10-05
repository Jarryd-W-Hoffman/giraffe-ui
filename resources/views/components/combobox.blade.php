@aware(['guiField' => null])

@props([
    'options' => [],
    'value' => null,
    'placeholder' => null,
    'size' => 'md',
    'invalid' => null,
    'empty' => null,
    'url' => null,
    'debounce' => 300,
    'searchingText' => null,
    'multiple' => false,
])

@php
    $empty ??= __('giraffeui::ui.no_results');
    $searchingText ??= __('giraffeui::ui.searching');

    // wire:model and x-model bind the value on the root (x-modelable); everything else goes on the text input.
    $binding = $attributes->filter(fn ($v, $key) => str_starts_with($key, 'wire:model') || str_starts_with($key, 'x-model'));
    $inputAttributes = $attributes->except(array_keys($binding->getAttributes()));

    $control = \JayAitch\GiraffeUI\Support\Form::control($guiField, $inputAttributes, $errors ?? null, $invalid);
    $name = $control['name'];
    $id = $control['attributes']['id'] ?? \JayAitch\GiraffeUI\Support\Id::make('gui-combobox');
    $selected = $value ?? ($name && $binding->isEmpty() ? old($control['errorKey']) : null);

    // With url, options come from the server as you type. These are the ones shown before any search.
    $initial = collect($options)->map(fn ($label, $value) => ['value' => (string) $value, 'label' => (string) $label])->values()->all();
    $labelOf = fn ($value) => collect($initial)->firstWhere('value', (string) $value)['label'] ?? '';

    // With multiple, the value is a list, submitted as name[], and each choice shows as a removable chip.
    if ($multiple) {
        $selected = collect(is_iterable($selected) ? $selected : ($selected === null || $selected === '' ? [] : [$selected]))->map(fn ($value) => (string) $value)->values()->all();
        $labels = collect($selected)->mapWithKeys(fn ($value) => [$value => $labelOf($value)])->filter()->all();
    }
    $selectedLabel = $multiple || $selected === null ? '' : $labelOf($selected);

    $sizes = [
        'sm' => 'h-8 pl-2.5 text-sm',
        'md' => 'h-10 pl-3 text-sm',
        'lg' => 'h-12 pl-3.5 text-base',
    ];
@endphp

{{--
    WAI-ARIA combobox with a listbox popup. Needs Alpine (Livewire includes it).
    With url, typing fetches {url}?q=… (debounced) and shows the JSON it returns: [{"value": …, "label": …}, …]
    or {"value": "label", …}. Any route works; nothing here depends on Livewire.
--}}
<div
    class="relative"
    x-data="{
        open: false,
        query: '',
        // Only filter once the user types; reopening shows every option with the current one highlighted.
        filtering: false,
        multiple: @js((bool) $multiple),
        value: @js($multiple ? $selected : ($selected === null ? '' : (string) $selected)),
        active: null,
        // With multiple, the labels of chosen options (they may not be among the current results), and what to announce.
        labels: @js($multiple ? (object) $labels : (object) []),
        announcement: '',
        // Searching on the server: the options to show, the query they're for, and the request in flight.
        url: @js($url),
        results: [],
        sent: '',
        searching: false,
        timer: null,
        request: null,
        enterPending: false,
        // The selected option may not be among the current results, so remember its label.
        selectedLabel: @js($selectedLabel),

        init() {
            this.results = this.initial();
            this.remember(this.value);
            this.query = this.multiple ? '' : this.labelFor(this.value);
            this.$watch('value', (value) => {
                this.remember(value);
                if (! this.open && ! this.multiple) this.query = this.labelFor(value);
            });
        },
        isSelected(value) {
            return this.multiple ? this.value.includes(String(value)) : this.value === String(value);
        },
        // A chip's label: from the list if the option is showing, otherwise as remembered.
        chipLabel(value) {
            return this.options().find((option) => option.dataset.value === value)?.dataset.label ?? this.labels[value] ?? value;
        },
        remove(value) {
            const label = this.chipLabel(value);
            this.value = this.value.filter((each) => each !== value);
            this.announcement = @js(__('giraffeui::ui.removed')).replace(':label', label);
            this.changed();
            this.$refs.text.focus();
        },
        changed() {
            this.$nextTick(() => this.$el.dispatchEvent(new Event('change', { bubbles: true })));
        },
        // Read from a script tag rather than x-data, so a Livewire re-render that changes them doesn't reset the component.
        initial() {
            return this.$refs.initial ? JSON.parse(this.$refs.initial.textContent) : [];
        },
        // wire:model and x-model set the value from outside, so note its label while its option is in the list.
        remember(value) {
            for (const each of this.multiple ? value : [value]) {
                const option = this.options().find((option) => option.dataset.value === String(each));
                if (! option) continue;
                this.multiple ? (this.labels[each] = option.dataset.label) : (this.selectedLabel = option.dataset.label);
            }
        },
        options() {
            return [...this.$refs.listbox.querySelectorAll('[role=option]')];
        },
        matches(option) {
            return this.url !== null || ! this.filtering || option.dataset.label.toLowerCase().includes(this.query.trim().toLowerCase());
        },
        visible() {
            return this.options().filter((option) => this.matches(option));
        },
        labelFor(value) {
            const option = this.options().find((option) => option.dataset.value === String(value));
            if (option) return option.dataset.label;
            return value !== '' && String(value) === String(this.value) ? this.selectedLabel : '';
        },
        show() {
            if (this.open) return;
            this.open = true;
            this.$refs.popup.showPopover?.();
            this.active = this.options().find((option) => this.isSelected(option.dataset.value))?.id ?? null;
        },
        hide() {
            if (! this.open) return;
            this.open = false;
            this.filtering = false;
            this.$refs.popup.hidePopover?.();
            this.active = null;
            this.query = this.multiple ? '' : this.labelFor(this.value);
            // Reopening shows the first options again, not the last search's results.
            if (this.url !== null) {
                clearTimeout(this.timer);
                this.request?.abort();
                this.results = this.initial();
                this.sent = '';
                this.searching = false;
                this.enterPending = false;
            }
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
            // With multiple, picking toggles the option and the list stays open for the next one.
            if (this.multiple) {
                const value = option.dataset.value;
                const adding = ! this.value.includes(value);
                this.labels[value] = option.dataset.label;
                this.value = adding ? [...this.value, value] : this.value.filter((each) => each !== value);
                this.announcement = @js(__('giraffeui::ui.added')).replace(':label', option.dataset.label);
                if (! adding) this.announcement = @js(__('giraffeui::ui.removed')).replace(':label', option.dataset.label);
                if (this.filtering) {
                    this.query = '';
                    this.filtering = false;
                    if (this.url !== null) this.find('');
                }
                this.active = option.id;
                return this.changed();
            }
            this.value = option.dataset.value;
            this.selectedLabel = option.dataset.label;
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
            if (this.url !== null) this.find(this.query.trim());
        },
        // Enter while a search is still running picks the first result once it arrives.
        enter() {
            if (this.searching) {
                this.enterPending = true;
                return;
            }
            this.choose(this.current());
        },
        find(query) {
            clearTimeout(this.timer);
            if (query === this.sent && ! this.searching) return;
            this.searching = true;
            this.timer = setTimeout(async () => {
                // A newer search replaces an older one, so a slow response can't overwrite a fresh one.
                this.request?.abort();
                const request = this.request = new AbortController();
                const url = new URL(this.url, window.location.href);
                url.searchParams.set('q', query);
                try {
                    const response = await fetch(url, { headers: { Accept: 'application/json' }, signal: request.signal });
                    const data = response.ok ? await response.json() : [];
                    this.results = Array.isArray(data)
                        ? data.map((option) => ({ value: String(option.value), label: String(option.label) }))
                        : Object.entries(data).map(([value, label]) => ({ value, label: String(label) }));
                } catch (error) {
                    if (error.name === 'AbortError') return;
                    this.results = [];
                }
                this.sent = query;
                this.searching = false;
                this.$nextTick(() => {
                    this.active = this.open ? (this.visible()[0]?.id ?? null) : null;
                    if (this.enterPending) {
                        this.enterPending = false;
                        this.choose(this.visible()[0]);
                    }
                });
            }, query === '' ? 0 : @js((int) $debounce));
        },
    }"
    x-modelable="value"
    x-on:click.outside="hide()"
    {{ $binding }}
>
    @if ($multiple)
        {{-- One hidden input per chosen value, submitted as name[]. --}}
        @if ($name)
            <template x-for="chosen in value" x-bind:key="chosen">
                <input type="hidden" name="{{ \Illuminate\Support\Str::finish($name, '[]') }}" x-bind:value="chosen">
            </template>
        @endif

        {{--
            The bordered box holds the chips and the text input, and the list is anchored to it. Chips have their own
            remove buttons, Backspace in an empty search removes the last one, and changes are announced.
        --}}
        <div
            style="anchor-name: {{ \JayAitch\GiraffeUI\Support\Anchor::name($id) }}"
            x-on:click.self="$refs.text.focus()"
            @class([
                'flex w-full min-w-0 flex-wrap items-center gap-1 rounded-gui border border-gui-input bg-white pr-9 text-zinc-900 shadow-xs transition-colors',
                'has-[input:focus]:outline-2 has-[input:focus]:-outline-offset-1 has-[input:focus]:outline-gui-primary-text',
                'has-aria-invalid:border-gui-danger-text has-disabled:cursor-not-allowed has-disabled:bg-zinc-100 dark:has-disabled:bg-zinc-800',
                'dark:bg-zinc-900 dark:text-zinc-100',
                ['sm' => 'min-h-8 py-0.5 pl-1 text-sm', 'md' => 'min-h-10 py-1 pl-1.5 text-sm', 'lg' => 'min-h-12 py-1.5 pl-2 text-base'][$size] ?? 'min-h-10 py-1 pl-1.5 text-sm',
                $inputAttributes->get('class'),
            ])
        >
            <template x-for="chosen in value" x-bind:key="chosen">
                <span class="inline-flex max-w-full items-center gap-1 rounded-[calc(var(--radius-gui)-2px)] bg-zinc-100 py-0.5 pr-0.5 pl-2 text-sm text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200">
                    <span class="truncate" x-text="chipLabel(chosen)"></span>
                    <button
                        type="button"
                        x-on:click="remove(chosen)"
                        x-bind:aria-label="@js(__('giraffeui::ui.remove')).replace(':label', chipLabel(chosen))"
                        @disabled($inputAttributes->get('disabled'))
                        class="flex size-5 cursor-pointer items-center justify-center rounded-sm text-zinc-500 hover:bg-zinc-200 hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-gui-primary-text dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-zinc-100"
                    >
                        <x-gui::icon name="x-mark" size="xs" />
                    </button>
                </span>
            </template>
            <input
        type="text"
        role="combobox"
        autocomplete="off"
        aria-autocomplete="list"
        aria-controls="{{ $id }}-listbox"
        aria-expanded="false"
        x-ref="text"
        x-bind:aria-expanded="open"
        x-bind:aria-activedescendant="active"
        x-model="query"
        x-on:input="typed()"
        x-on:click="open ? hide() : show()"
        x-on:keydown.down.prevent="$event.altKey ? show() : move(1)"
        x-on:keydown.up.prevent="move(-1)"
        x-on:keydown.enter="if (open) { $event.preventDefault(); enter(); }"
        x-on:keydown.tab="hide()"
        x-on:blur="hide()"
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
                x-on:keydown.escape.prevent="open ? hide() : (query = '')"
                x-on:keydown.backspace="if (query === '' && value.length) { $event.preventDefault(); remove(value[value.length - 1]); }"
                {{ $inputAttributes
                    ->except(['aria-describedby', 'name', 'value', 'id', 'class'])
                    ->merge(array_diff_key($control['attributes'], ['name' => true]) + ['id' => $id])
                    ->class('h-7 min-w-24 flex-1 border-0 bg-transparent px-1 placeholder:text-zinc-500 focus:outline-none disabled:cursor-not-allowed dark:placeholder:text-zinc-400') }}
            >
        </div>

        <span class="sr-only" aria-live="polite" x-text="announcement"></span>
    @else
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
        x-ref="text"
        x-bind:aria-expanded="open"
        x-bind:aria-activedescendant="active"
        x-model="query"
        x-on:input="typed()"
        x-on:click="open ? hide() : show()"
        x-on:keydown.down.prevent="$event.altKey ? show() : move(1)"
        x-on:keydown.up.prevent="move(-1)"
        x-on:keydown.enter="if (open) { $event.preventDefault(); enter(); }"
        x-on:keydown.tab="hide()"
        x-on:blur="hide()"
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            x-on:keydown.escape.prevent="open ? hide() : (value = '', query = '')"
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
                ])
                ->style('anchor-name: '.\JayAitch\GiraffeUI\Support\Anchor::name($id)) }}
        >
    @endif

    {{-- Pinned to the control, so width classes on it move the icon and size the list too. --}}
    <x-gui::icon name="chevron-up-down" size="sm" class="pointer-events-none absolute inset-y-0 right-0 my-auto mr-3 text-zinc-500 dark:text-zinc-400" :style="\JayAitch\GiraffeUI\Support\Anchor::pin($id, 'right')" />

    {{-- A manual popover in the top layer, so the list isn't clipped by modals or scrolling containers. --}}
    <div
        x-ref="popup"
        popover="manual"
        style="position-anchor: {{ \JayAitch\GiraffeUI\Support\Anchor::name($id) }}"
        x-on:mousedown.prevent
        class="inset-auto m-0 my-1 max-h-60 w-[anchor-size(width)] overflow-y-auto rounded-gui border border-zinc-200 bg-white p-1 text-sm text-zinc-900 shadow-lg [position-area:bottom_span-right] [position-try-fallbacks:flip-block] dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-100"
    >
        {{-- Above the list, so "Searching…" shows even when the list is scrolled. --}}
        <p x-show="open && (searching || visible().length === 0)" class="px-2.5 py-1.5 text-zinc-500 dark:text-zinc-400" role="status" x-text="searching ? @js($searchingText) : @js($empty)">{{ $empty }}</p>
        {{-- The previous results fade while a search runs. --}}
        <ul id="{{ $id }}-listbox" role="listbox" x-ref="listbox" @if ($multiple) aria-multiselectable="true" @endif x-bind:aria-busy="searching" class="transition-opacity aria-busy:opacity-70">
            @if ($url)
                {{-- JSON_HEX_TAG escapes < and >, so labels can't close the script tag. --}}
                <script type="application/json" x-ref="initial">{!! json_encode($initial, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
                <template x-for="(option, index) in results" x-bind:key="option.value">
                    <li
                        x-bind:id="@js($id.'-option-') + index"
                        role="option"
                        x-bind:data-value="option.value"
                        x-bind:data-label="option.label"
                        x-bind:aria-selected="isSelected(option.value)"
                        x-on:click="choose($el)"
                        x-on:mousemove="active = $el.id"
                        x-bind:data-active="active === $el.id"
                        class="group/option flex cursor-pointer items-center justify-between gap-2 rounded-[calc(var(--radius-gui)-2px)] px-2.5 py-1.5 data-[active=true]:bg-zinc-100 dark:data-[active=true]:bg-zinc-800"
                    >
                        <span x-text="option.label"></span>
                        <x-gui::icon name="check" size="sm" class="invisible text-gui-primary-text group-aria-selected/option:visible" />
                    </li>
                </template>
            @endif
            @foreach ($url ? [] : $options as $optionValue => $optionLabel)
                <li
                    id="{{ $id }}-option-{{ $loop->index }}"
                    role="option"
                    data-value="{{ $optionValue }}"
                    data-label="{{ $optionLabel }}"
                    aria-selected="{{ ($multiple ? in_array((string) $optionValue, $selected, true) : (string) $optionValue === (string) $selected) ? 'true' : 'false' }}"
                    x-bind:aria-selected="isSelected(@js((string) $optionValue))"
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
    </div>
</div>
