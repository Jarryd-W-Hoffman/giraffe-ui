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
                <x-gui::icon name="chevron-down" size="sm" />
            </x-slot:trailing>
        </x-gui::button>
    @endisset
</span>

{{--
    A native popover: Escape and clicking outside close it, and focus returns to the trigger.
    Alpine adds the menu keyboard pattern: focus the first item on open, arrow keys, Home and End. Submenus
    (dropdown.submenu) are nested popovers, so the same keys work in whichever menu has focus, ArrowRight opens a
    submenu and ArrowLeft closes it.
--}}
<div
    id="{{ $id }}"
    popover
    role="menu"
    style="position-anchor: {{ $anchor }}"
    x-data="{
        // The menu (this one or an open submenu) that an element belongs to.
        menuOf(element) {
            return element?.closest('[role=menu]') ?? this.$el;
        },
        items(menu = this.$el) {
            return [...menu.querySelectorAll('[role^=menuitem]:not([aria-disabled=true]):not(:disabled)')]
                .filter((item) => item.closest('[role=menu]') === menu);
        },
        move(step) {
            const items = this.items(this.menuOf(document.activeElement));
            const current = items.indexOf(document.activeElement);
            const next = step === 'first' ? 0 : step === 'last' ? items.length - 1 : (current + step + items.length) % items.length;
            items[next]?.focus();
        },
    }"
    x-on:toggle="$event.target === $el && $event.newState === 'open' && $nextTick(() => items()[0]?.focus())"
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
