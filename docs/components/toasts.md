# Toast

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

Brief, non-blocking messages announced to screen readers. Place &lt;x-gui::toasts /&gt; once in your layout.

```blade
{{-- In your layout --}}
<x-gui::toasts />

{{-- Anywhere --}}
<x-gui::button x-on:click="GiraffeUI.toast('Saved', { color: 'success' })">Save</x-gui::button>
```

## Colours and titles

Toasts dismiss after 5 seconds. Hovering or focusing one pauses it.

```blade
<x-gui::button variant="outline" color="secondary" x-on:click="GiraffeUI.toast('Your changes were saved.', { color: 'success', title: 'Saved' })">Success</x-gui::button>
<x-gui::button variant="outline" color="secondary" x-on:click="GiraffeUI.toast('Check your connection and try again.', { color: 'danger', title: 'Upload failed', duration: 0 })">Error (stays)</x-gui::button>
<x-gui::button variant="outline" color="secondary" x-on:click="$dispatch('gui-toast', { message: 'Sent with $dispatch' })">From Alpine</x-gui::button>
```

## Above a modal

The region lives in the top layer, so toasts appear above open dialogs.

```blade
<x-gui::button variant="outline" commandfor="toast-modal" command="show-modal">Open modal</x-gui::button>

<x-gui::modal id="toast-modal" title="Invite a teammate" size="sm">
    <x-gui::field label="Email" name="invite"><x-gui::input type="email" value="grace@example.com" /></x-gui::field>
    <x-slot:footer>
        <x-gui::modal.close>Close</x-gui::modal.close>
        <x-gui::button x-on:click="GiraffeUI.toast('Invite sent to grace@example.com', { color: 'success' })">Send invite</x-gui::button>
    </x-slot:footer>
</x-gui::modal>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `duration` | `int` | `5000` | Default time on screen, in milliseconds. 0 keeps toasts until dismissed. |

Options for each toast: message, title, color (primary, secondary, success, warning, danger, info) and duration.

From Livewire: $this-&gt;dispatch('gui-toast', message: 'Saved', color: 'success').

After a redirect: session()-&gt;flash('gui-toast', 'Saved') or an array of options.

While a modal is open, toasts still appear and are announced, but the browser makes everything outside the modal unclickable, so they dismiss on their own timer.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
