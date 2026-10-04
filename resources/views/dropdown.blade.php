@php
    $anchor = \JayAitch\GiraffeUI\Support\Anchor::name($id);
@endphp

<span class="inline-flex" style="anchor-name: {{ $anchor }}">
    @isset($trigger)
        {{ $trigger }}
    @else
        <x-gui::button variant="outline" color="secondary" :commandfor="$id" command="toggle-popover" aria-haspopup="menu">
            {{ $label }}
            <x-slot:trailing>
                <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
            </x-slot:trailing>
        </x-gui::button>
    @endisset
</span>

{{--
    A native popover: Escape and clicking outside close it, and focus returns to the trigger.
    Alpine adds the menu keyboard pattern: focus the first item on open, arrow keys, Home and End.
--}}
<div
    id="{{ $id }}"
    popover
    role="menu"
    style="position-anchor: {{ $anchor }}"
    x-data="{
        items() {
            return [...this.$el.querySelectorAll('[role=menuitem]:not([aria-disabled=true])')];
        },
        move(step) {
            const items = this.items();
            const current = items.indexOf(document.activeElement);
            const next = step === 'first' ? 0 : step === 'last' ? items.length - 1 : (current + step + items.length) % items.length;
            items[next]?.focus();
        },
    }"
    x-on:toggle="$event.newState === 'open' && $nextTick(() => items()[0]?.focus())"
    x-on:keydown.down.prevent="move(1)"
    x-on:keydown.up.prevent="move(-1)"
    x-on:keydown.home.prevent="move('first')"
    x-on:keydown.end.prevent="move('last')"
    x-on:keydown.tab="$el.hidePopover()"
    {{ $attributes->class([
        'inset-auto m-0 min-w-48 max-w-[calc(100vw-1rem)] rounded-gui border border-zinc-200 bg-white p-1 text-sm text-zinc-900 shadow-lg dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-100',
        'opacity-0 transition-all transition-discrete duration-100 open:opacity-100 starting:open:opacity-0 motion-reduce:transition-none',
        \JayAitch\GiraffeUI\Support\Anchor::position($position),
    ]) }}
>
    {{ $slot }}
</div>
