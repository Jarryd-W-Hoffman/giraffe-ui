{{-- Opens the docs' contents (the sidebar) on small screens. --}}
<button
    type="button"
    class="-ms-1 me-1 flex size-9 cursor-pointer items-center justify-center rounded-full text-zinc-600 hover:bg-zinc-100 focus-visible:outline-2 focus-visible:outline-gui-primary-text md:hidden dark:text-zinc-400 dark:hover:bg-zinc-900"
    x-on:click="menu = ! menu"
    x-bind:aria-expanded="menu"
    aria-controls="sidebar"
    aria-label="Browse the docs"
>
    <x-gui::icon name="squares-2x2" size="sm" />
</button>
