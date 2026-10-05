# Banner

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A full-width notice for the top of a page: an announcement, a warning or a reminder. It can be dismissed, and remember that it was.

```blade
<x-gui::banner>We're launching on Tuesday.</x-gui::banner>
```

## Announcement

Solid by default, in the primary colour. Put a link or button in the action slot; underline links in solid banners so they stand out from the text.

```blade
<x-gui::banner icon="information-circle">
    GiraffeUI 0.1 is out, with 47 components.
    <x-slot:action>
        <a href="#" class="font-semibold underline underline-offset-2">Read the notes</a>
    </x-slot:action>
</x-gui::banner>
```

## Soft, in any colour

```blade
<div class="space-y-3">
    <x-gui::banner variant="soft" color="warning" icon="exclamation-triangle">Your trial ends in 3 days. Add a card to keep your shop open.</x-gui::banner>
    <x-gui::banner variant="soft" color="success" icon="check-circle">Payouts are back to normal.</x-gui::banner>
</div>
```

## Dismissible, and remembered

dismissible adds a close button. With remember, a dismissed banner stays hidden on later visits in this browser, without flashing as the page loads. Dismissing also fires banner-dismissed, so you can record it on the server too.

```blade
<x-gui::banner color="secondary" dismissible remember="catalog-demo">
    We've updated our privacy policy.
</x-gui::banner>

<x-gui::button size="sm" variant="outline" color="secondary" class="mt-3" x-on:click="localStorage.removeItem('gui-banner:catalog-demo'); location.reload()">Show it again</x-gui::button>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `color` | `'primary' \| 'secondary' \| 'success' \| 'warning' \| 'danger' \| 'info'` | `'primary'` |  |
| `variant` | `'solid' \| 'soft'` | `'solid'` |  |
| `icon` | `string` | `null` | An icon name, or use the icon slot. |
| `dismissible` | `bool` | `false` | Adds a close button (needs Alpine). |
| `remember` | `string` | `null` | A key: once dismissed, the banner stays hidden on later visits in this browser. |
| `label` | `string` | `'Announcement'` | Names the banner's region for screen readers (translatable). |

Listen for banner-dismissed (detail: { key }) to store the dismissal for a signed-in user, e.g. x-on:banner-dismissed.window="fetch(…)" or with Livewire.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `default` | The message. |
| `action` | A link or button after the message. |
| `icon` | Your own icon. |
