# Modal

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A dialog over the page. Built on the native &lt;dialog&gt; element, so focus trapping, Escape and the backdrop work without JavaScript.

```blade
<x-gui::button commandfor="confirm" command="show-modal">Open</x-gui::button>

<x-gui::modal id="confirm" title="Are you sure?">
    …
</x-gui::modal>
```

## Confirm

Buttons open and close the dialog with the command and commandfor attributes. No JavaScript needed.

```blade
<x-gui::button color="danger" commandfor="delete-project" command="show-modal">Delete project</x-gui::button>

<x-gui::modal id="delete-project" title="Delete project?" description="This permanently deletes the project and its deployments." size="sm">
    <x-slot:footer>
        <x-gui::modal.close>Cancel</x-gui::modal.close>
        <x-gui::button color="danger">Delete</x-gui::button>
    </x-slot:footer>
</x-gui::modal>
```

## With a form

Put the form around the body and footer. After a failed submission, reopen it with :open="$errors-&gt;any()".

```blade
<x-gui::button variant="outline" commandfor="edit-profile" command="show-modal">Edit profile</x-gui::button>

<x-gui::modal id="edit-profile" title="Edit profile" description="Changes are visible to your team.">
    <form id="edit-profile-form" class="space-y-4" onsubmit="event.preventDefault(); this.closest('dialog').close()">
        <x-gui::field label="Name" name="name">
            <x-gui::input value="Ada Lovelace" />
        </x-gui::field>
        <x-gui::field label="Bio" name="bio">
            <x-gui::textarea>Analyst, metaphysician and founder of scientific computing.</x-gui::textarea>
        </x-gui::field>
    </form>

    <x-slot:footer>
        <x-gui::modal.close>Cancel</x-gui::modal.close>
        <x-gui::button type="submit" form="edit-profile-form">Save</x-gui::button>
    </x-slot:footer>
</x-gui::modal>
```

## Required action

With :dismissible="false" there is no close button, and Escape and backdrop clicks are ignored where the browser supports closedby.

```blade
<x-gui::button variant="outline" color="secondary" commandfor="terms" command="show-modal">Show terms</x-gui::button>

<x-gui::modal id="terms" title="Updated terms" :dismissible="false" size="sm">
    We've updated our terms of service. Please accept them to continue.

    <x-slot:footer>
        <x-gui::button commandfor="terms" command="close">Accept</x-gui::button>
    </x-slot:footer>
</x-gui::modal>
```

## Long content

The body scrolls; the header and footer stay put.

```blade
<x-gui::button variant="ghost" commandfor="changelog" command="show-modal">What's new</x-gui::button>

<x-gui::modal id="changelog" title="What's new" size="lg">
    <div class="space-y-4">
        @foreach (range(1, 12) as $release)
            <div>
                <p class="font-medium">Release 1.{{ $release }}</p>
                <p class="text-zinc-600 dark:text-zinc-400">Faster deploys, fewer bugs and a better dark mode.</p>
            </div>
        @endforeach
    </div>

    <x-slot:footer>
        <x-gui::modal.close>Close</x-gui::modal.close>
    </x-slot:footer>
</x-gui::modal>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `id` | `string` | `required` | Used by buttons to open and close it. |
| `title` | `string` | `null` | Heading, linked with aria-labelledby. |
| `description` | `string` | `null` | Text under the title, linked with aria-describedby. |
| `size` | `'sm' \| 'md' \| 'lg' \| 'xl'` | `'md'` | Maximum width. |
| `dismissible` | `bool` | `true` | Show a close button and allow Escape and backdrop clicks. |
| `open` | `bool` | `false` | Open when the page loads, e.g. :open="$errors-&gt;any()". |

Open with &lt;button commandfor="id" command="show-modal"&gt;, close with command="close" or &lt;x-gui::modal.close&gt;.

From JavaScript: document.getElementById('id').showModal() and .close().

From Livewire: $this-&gt;js("document.getElementById('id').showModal()"). The dialog has wire:ignore.self, so re-renders don't close it.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `default` | The body. Scrolls when it is tall. |
| `footer` | Actions. Stacked on small screens, right-aligned on larger ones. |
