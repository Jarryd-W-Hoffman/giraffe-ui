{{--
    A menuitemcheckbox that is a real checkbox: it toggles natively (Space, click, Enter), submits with name and
    value, and works with wire:model or x-model. Toggling leaves the menu open. Attributes go on the checkbox.
--}}
<label @class([
    'group/choice flex w-full cursor-pointer items-center gap-2 rounded-[calc(var(--radius-gui)-2px)] px-2.5 py-1.5 whitespace-nowrap transition-colors',
    'has-focus-visible:outline-2 has-focus-visible:-outline-offset-2 has-focus-visible:outline-gui-primary-text',
    'has-disabled:pointer-events-none has-disabled:opacity-50',
    'text-zinc-700 hover:bg-zinc-100 hover:text-zinc-900 has-focus:bg-zinc-100 has-focus:text-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-zinc-100 dark:has-focus:bg-zinc-800 dark:has-focus:text-zinc-100',
])>
    <input type="checkbox" role="menuitemcheckbox" x-on:keydown.enter.prevent="$el.click()" {{ $attributes->class('peer sr-only') }}>
    <span class="flex size-4 shrink-0 items-center justify-center rounded-sm border border-zinc-300 peer-checked:border-gui-primary peer-checked:bg-gui-primary peer-checked:text-gui-primary-foreground dark:border-zinc-600" aria-hidden="true">
        <x-gui::icon name="check" size="xs" class="invisible group-has-checked/choice:visible" />
    </span>
    {{ $leading ?? '' }}{{ $slot }}
</label>
