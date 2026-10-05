@props([
    'label',
    'disabled' => false,
])

@php
    $id = \JayAitch\GiraffeUI\Support\Id::make('gui-submenu');
    $anchor = \JayAitch\GiraffeUI\Support\Anchor::name($id.'-trigger');
@endphp

{{--
    A nested popover beside its trigger, so it stays open with the parent menu and closes with it.
    Click, Enter, Space or ArrowRight open it and focus its first item; ArrowLeft or Escape close it and return
    focus to the trigger. Hovering opens it too, and moving away closes it after a moment.
--}}
<div
    x-data="{
        open: false,
        timer: null,
        // The popover's real state; toggle events arrive later, so fast key presses can outrun them.
        isOpen() {
            return this.$refs.panel.matches(':popover-open');
        },
        // Keyboard and click opens move focus into the submenu; hovering doesn't.
        show(focus) {
            clearTimeout(this.timer);
            if (! this.isOpen()) this.$refs.panel.showPopover();
            this.open = true;
            // Synchronously: the popover is already open, and $nextTick (a timer) can lose a race with fast keys.
            if (focus) this.first();
        },
        hideSoon() {
            clearTimeout(this.timer);
            this.timer = setTimeout(() => this.isOpen() && this.$refs.panel.hidePopover(), 200);
        },
        close() {
            clearTimeout(this.timer);
            if (this.isOpen()) this.$refs.panel.hidePopover();
            this.open = false;
            this.$refs.trigger.focus();
        },
        first() {
            [...this.$refs.panel.querySelectorAll('[role^=menuitem]:not([aria-disabled=true]):not(:disabled)')]
                .find((item) => item.closest('[role=menu]') === this.$refs.panel)?.focus();
        },
        toggled() {
            this.open = this.isOpen();
            // Closed some other way (the parent closing, a click elsewhere): don't strand focus inside it.
            if (! this.open && this.$refs.panel.contains(document.activeElement)) this.$refs.trigger.focus();
        },
    }"
    x-on:mouseenter="show(false)"
    x-on:mouseleave="hideSoon()"
>
    <button
        type="button"
        role="menuitem"
        x-ref="trigger"
        aria-haspopup="menu"
        aria-expanded="false"
        x-bind:aria-expanded="open"
        aria-controls="{{ $id }}"
        @if ($disabled) aria-disabled="true" @endif
        x-on:click="show(true)"
        x-on:keydown.right.prevent.stop="show(true)"
        x-on:keydown.enter.prevent="show(true)"
        x-on:keydown.space.prevent="show(true)"
        style="anchor-name: {{ $anchor }}"
        {{ $attributes->class([
            'flex w-full cursor-pointer items-center gap-2 rounded-[calc(var(--radius-gui)-2px)] px-2.5 py-1.5 text-left whitespace-nowrap transition-colors',
            'focus:outline-none focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-gui-primary-text',
            'aria-disabled:pointer-events-none aria-disabled:opacity-50',
            'text-zinc-700 hover:bg-zinc-100 hover:text-zinc-900 focus:bg-zinc-100 focus:text-zinc-900 aria-expanded:bg-zinc-100 aria-expanded:text-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-zinc-100 dark:focus:bg-zinc-800 dark:focus:text-zinc-100 dark:aria-expanded:bg-zinc-800 dark:aria-expanded:text-zinc-100',
        ]) }}
    >
        {{ $leading ?? '' }}{{ $label }}
        <x-gui::icon name="chevron-right" size="sm" class="ml-auto text-zinc-500 dark:text-zinc-400" />
    </button>

    <div
        id="{{ $id }}"
        x-ref="panel"
        popover
        role="menu"
        aria-label="{{ $label }}"
        style="position-anchor: {{ $anchor }}"
        x-on:toggle="toggled()"
        x-on:keydown.left.prevent.stop="close()"
        x-on:keydown.escape.prevent.stop="close()"
        class="inset-auto m-0 -mt-[5px] ml-1 min-w-48 max-w-[calc(100vw-1rem)] rounded-gui border border-zinc-200 bg-white p-1 text-sm text-zinc-900 shadow-lg [position-area:right_span-bottom] [position-try-fallbacks:flip-inline,bottom_span-left,bottom_span-right] dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-100"
    >
        {{ $slot }}
    </div>
</div>
