@php
    // Every class is written out in full so Tailwind can find it when scanning this file.
    // Selected styles key off aria-selected, which both the server and Alpine set.
    $variants = [
        'underline' => [
            'list' => 'flex gap-6 border-b border-zinc-200 dark:border-zinc-800',
            'tab' => '-mb-px border-b-2 border-transparent pb-2.5 text-zinc-600 hover:border-zinc-300 hover:text-zinc-900 aria-selected:border-gui-primary-text aria-selected:text-zinc-900 dark:text-zinc-400 dark:hover:border-zinc-700 dark:hover:text-zinc-100 dark:aria-selected:text-zinc-100',
        ],
        'pills' => [
            'list' => 'inline-flex gap-1 rounded-gui bg-zinc-100 p-1 dark:bg-zinc-800',
            'tab' => 'rounded-gui px-3 py-1.5 text-zinc-600 hover:text-zinc-900 aria-selected:bg-white aria-selected:text-zinc-900 aria-selected:shadow-xs dark:text-zinc-400 dark:hover:text-zinc-100 dark:aria-selected:bg-zinc-950 dark:aria-selected:text-zinc-100',
        ],
    ];

    $style = $variants[$variant] ?? $variants['underline'];
@endphp

<div
    x-data="{
        selected: @js($selected),
        move(step) {
            const tabs = [...this.$refs.list.querySelectorAll('[role=tab]:not([disabled])')];
            const current = tabs.indexOf(document.activeElement);
            const next = step === 'first' ? 0 : step === 'last' ? tabs.length - 1 : (current + step + tabs.length) % tabs.length;
            tabs[next].focus();
            tabs[next].click();
        },
    }"
    x-modelable="selected"
    {{ $attributes }}
>
    <div
        role="tablist"
        x-ref="list"
        x-on:keydown.right.prevent="move(1)"
        x-on:keydown.left.prevent="move(-1)"
        x-on:keydown.home.prevent="move('first')"
        x-on:keydown.end.prevent="move('last')"
        class="{{ $style['list'] }}"
    >
        @foreach ($tabs as $name => $label)
            <button
                type="button"
                role="tab"
                id="{{ $tabId($name) }}"
                aria-controls="{{ $panelId($name) }}"
                aria-selected="{{ $name === $selected ? 'true' : 'false' }}"
                tabindex="{{ $name === $selected ? '0' : '-1' }}"
                x-bind:aria-selected="selected === @js($name) ? 'true' : 'false'"
                x-bind:tabindex="selected === @js($name) ? 0 : -1"
                x-on:click="selected = @js($name)"
                class="{{ $style['tab'] }} cursor-pointer text-sm font-medium whitespace-nowrap transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text"
            >{{ $label }}</button>
        @endforeach
    </div>

    {{ $slot }}
</div>
