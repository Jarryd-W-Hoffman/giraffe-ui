# Drawer

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A panel that slides in from the side. The same native &lt;dialog&gt; as Modal, so focus trapping, Escape and the backdrop work without JavaScript.

```blade
<x-gui::button commandfor="cart" command="show-modal">Cart</x-gui::button>

<x-gui::drawer id="cart" title="Your cart">
    …
</x-gui::drawer>
```

## Cart

```blade
<x-gui::button variant="outline" commandfor="cart" command="show-modal">Open cart</x-gui::button>

<x-gui::drawer id="cart" title="Your cart" description="2 items">
    <ul class="divide-y divide-zinc-200 dark:divide-zinc-800">
        <li class="flex justify-between py-3"><span>GiraffeUI hoodie</span><span class="font-medium">$59</span></li>
        <li class="flex justify-between py-3"><span>Sticker pack</span><span class="font-medium">$6</span></li>
    </ul>

    <x-slot:footer>
        <x-gui::modal.close>Keep shopping</x-gui::modal.close>
        <x-gui::button>Checkout · $65</x-gui::button>
    </x-slot:footer>
</x-gui::drawer>
```

## Navigation from the left

```blade
<x-gui::button variant="ghost" color="secondary" commandfor="mobile-nav" command="show-modal">
    <x-slot:leading><x-gui::icon name="chevron-right" size="sm" /></x-slot:leading>
    Menu
</x-gui::button>

<x-gui::drawer id="mobile-nav" side="left" size="sm" title="GiraffeUI">
    <nav aria-label="Main" class="-mx-2 flex flex-col">
        <a href="#" class="rounded-gui px-2 py-2 hover:bg-zinc-100 dark:hover:bg-zinc-800">Dashboard</a>
        <a href="#" class="rounded-gui px-2 py-2 hover:bg-zinc-100 dark:hover:bg-zinc-800">Projects</a>
        <a href="#" class="rounded-gui px-2 py-2 hover:bg-zinc-100 dark:hover:bg-zinc-800">Settings</a>
    </nav>
</x-gui::drawer>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `id` | `string` | `required` | Used by buttons to open and close it. |
| `side` | `'right' \| 'left'` | `'right'` | The edge it slides in from. |
| `title` | `string` | `null` | Heading, linked with aria-labelledby. |
| `description` | `string` | `null` | Linked with aria-describedby. |
| `size` | `'sm' \| 'md' \| 'lg' \| 'xl'` | `'md'` | Maximum width. |
| `dismissible` | `bool` | `true` | Close button, Escape and backdrop clicks. |
| `open` | `bool` | `false` | Open when the page loads. |

Open with &lt;button commandfor="id" command="show-modal"&gt;. &lt;x-gui::modal.close&gt; works inside drawers too.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `default` | The body. Scrolls when it is tall. |
| `footer` | Actions, pinned to the bottom. |
