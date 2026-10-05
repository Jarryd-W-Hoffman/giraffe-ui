{{--
    A menuitemradio that is a real radio button: give the radios in a set the same name (and wire:model or x-model).
    Picking one leaves the menu open. Put a set in dropdown.group to label it. Attributes go on the radio.
--}}
<label @class([
    'group/choice flex w-full cursor-pointer items-center gap-2 rounded-[calc(var(--radius-gui)-2px)] px-2.5 py-1.5 whitespace-nowrap transition-colors',
    'has-focus-visible:outline-2 has-focus-visible:-outline-offset-2 has-focus-visible:outline-gui-primary-text',
    'has-disabled:pointer-events-none has-disabled:opacity-50',
    'text-zinc-700 hover:bg-zinc-100 hover:text-zinc-900 has-focus:bg-zinc-100 has-focus:text-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-zinc-100 dark:has-focus:bg-zinc-800 dark:has-focus:text-zinc-100',
])>
    <input type="radio" role="menuitemradio" x-on:keydown.enter.prevent="$el.click()" x-on:keydown.left.prevent x-on:keydown.right.prevent {{ $attributes->class('peer sr-only') }}>
    <span class="flex size-4 shrink-0 items-center justify-center rounded-full border border-zinc-300 peer-checked:border-gui-primary dark:border-zinc-600" aria-hidden="true">
        <span class="invisible size-2 rounded-full bg-gui-primary group-has-checked/choice:visible"></span>
    </span>
    {{ $leading ?? '' }}{{ $slot }}
</label>
