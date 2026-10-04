{{--
    A native <dialog> (focus trapping, Escape, backdrop) holding a combobox and a grouped listbox.
    Needs Alpine (Livewire includes it). Open with the shortcut, or <button commandfor="{id}" command="show-modal">.
--}}
<dialog
    id="{{ $id }}"
    aria-label="{{ $label }}"
    closedby="any"
    wire:ignore.self
    x-data="{
        query: '',
        active: null,

        items() {
            return [...this.$refs.listbox.querySelectorAll('[role=option]')];
        },
        matches(item) {
            const query = this.query.trim().toLowerCase();
            return query === '' || item.dataset.search.includes(query);
        },
        visible() {
            return this.items().filter((item) => this.matches(item));
        },
        reset() {
            this.query = '';
            this.$nextTick(() => this.active = this.visible()[0]?.id ?? null);
        },
        move(step) {
            const items = this.visible();
            if (! items.length) return;
            let index = items.findIndex((item) => item.id === this.active);
            index = step === 'first' ? 0 : step === 'last' ? items.length - 1 : (index + step + items.length) % items.length;
            this.active = items[index].id;
            items[index].scrollIntoView({ block: 'nearest' });
        },
        // The highlighted item, or the first match if the highlight is stale (e.g. Enter straight after typing).
        current() {
            const item = document.getElementById(this.active);
            return item && this.matches(item) ? item : this.visible()[0];
        },
        run(item) {
            // The item's own click handler closes the palette, then it navigates or runs its action.
            if (item && item.getAttribute('aria-disabled') !== 'true') item.click();
        },
    }"
    x-init="reset()"
    x-on:toggle="$event.newState === 'open' ? (reset(), $nextTick(() => $refs.input.focus())) : null"
    @if ($shortcut)
        x-on:keydown.window.meta.{{ $shortcut }}.prevent="$el.open ? $el.close() : $el.showModal()"
        x-on:keydown.window.ctrl.{{ $shortcut }}.prevent="$el.open ? $el.close() : $el.showModal()"
    @endif
    {{ $attributes->class([
        'mx-auto mt-[12vh] w-[calc(100%-2rem)] max-w-xl overflow-hidden rounded-gui border border-zinc-200 bg-white p-0 text-zinc-900 shadow-2xl dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-100',
        'backdrop:bg-zinc-950/50 backdrop:backdrop-blur-[2px]',
        'opacity-0 scale-95 transition-all transition-discrete duration-150 open:opacity-100 open:scale-100 starting:open:opacity-0 starting:open:scale-95 motion-reduce:transition-none',
    ]) }}
>
    <div class="flex items-center gap-3 border-b border-zinc-200 px-4 dark:border-zinc-800">
        <x-gui::icon name="magnifying-glass" class="text-zinc-500 dark:text-zinc-400" />
        <input
            x-ref="input"
            type="text"
            role="combobox"
            aria-expanded="true"
            aria-controls="{{ $id }}-listbox"
            aria-autocomplete="list"
            aria-label="{{ $placeholder }}"
            x-bind:aria-activedescendant="active"
            autocomplete="off"
            placeholder="{{ $placeholder }}"
            x-model="query"
            x-on:input="$nextTick(() => active = visible()[0]?.id ?? null)"
            x-on:keydown.down.prevent="move(1)"
            x-on:keydown.up.prevent="move(-1)"
            x-on:keydown.home.prevent="move('first')"
            x-on:keydown.end.prevent="move('last')"
            x-on:keydown.enter.prevent="run(current())"
            class="h-12 w-full bg-transparent text-sm placeholder:text-zinc-500 focus:outline-none dark:placeholder:text-zinc-400"
        >
        @if ($shortcut)
            <span class="hidden shrink-0 items-center gap-1 sm:flex" aria-hidden="true"><x-gui::kbd>Esc</x-gui::kbd></span>
        @endif
    </div>

    <div id="{{ $id }}-listbox" role="listbox" aria-label="{{ $label }}" x-ref="listbox" class="max-h-80 overflow-y-auto p-2">
        {{ $slot }}
    </div>

    <p x-show="visible().length === 0" role="status" class="px-4 py-10 text-center text-sm text-zinc-600 dark:text-zinc-400">{{ $empty }}</p>
</dialog>
