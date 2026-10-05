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
    $selectedLabel = $selected === null ? '' : (collect($initial)->firstWhere('value', (string) $selected)['label'] ?? '');

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
        value: @js($selected === null ? '' : (string) $selected),
        active: null,
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
            this.query = this.labelFor(this.value);
            this.$watch('value', (value) => {
                this.remember(value);
                if (! this.open) this.query = this.labelFor(value);
            });
        },
        // Read from a script tag rather than x-data, so a Livewire re-render that changes them doesn't reset the component.
        initial() {
            return this.$refs.initial ? JSON.parse(this.$refs.initial.textContent) : [];
        },
        // wire:model and x-model set the value from outside, so note its label while its option is in the list.
        remember(value) {
            const option = this.options().find((option) => option.dataset.value === String(value));
            if (option) this.selectedLabel = option.dataset.label;
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
            this.active = this.options().find((option) => option.dataset.value === String(this.value))?.id ?? null;
        },
        hide() {
            if (! this.open) return;
            this.open = false;
            this.filtering = false;
            this.$refs.popup.hidePopover?.();
            this.active = null;
            this.query = this.labelFor(this.value);
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
        x-on:keydown.enter="if (open) { $event.preventDefault(); enter(); }"
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
            ])
            ->style('anchor-name: '.\JayAitch\GiraffeUI\Support\Anchor::name($id)) }}
    >

    {{-- Pinned to the text input, so width classes on it move the icon and size the list too. --}}
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
        <ul id="{{ $id }}-listbox" role="listbox" x-ref="listbox" x-bind:aria-busy="searching" class="transition-opacity aria-busy:opacity-70">
            @if ($url)
                {{-- JSON_HEX_TAG escapes < and >, so labels can't close the script tag. --}}
                <script type="application/json" x-ref="initial">{!! json_encode($initial, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
                <template x-for="(option, index) in results" x-bind:key="option.value">
                    <li
                        x-bind:id="@js($id.'-option-') + index"
                        role="option"
                        x-bind:data-value="option.value"
                        x-bind:data-label="option.label"
                        x-bind:aria-selected="value === option.value"
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
    </div>
</div>
