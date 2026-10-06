# Timeline

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

Events in order: an order's history, an activity feed, a changelog. An ordered list with a marker for each event and a line joining them.

```blade
<x-gui::timeline>
    <x-gui::timeline.item title="Order placed" time="5 Oct, 09:12" />
</x-gui::timeline>
```

## Order history

icon puts an icon in a coloured circle. Colour is decoration only, so say what happened in the title. time takes text or a date object (with format), and becomes a &lt;time&gt; element.

```blade
<x-gui::timeline label="Order history" class="max-w-md">
    <x-gui::timeline.item title="Order placed" time="5 Oct, 09:12" datetime="2026-10-05T09:12" icon="check" color="success" />
    <x-gui::timeline.item title="Payment confirmed" time="5 Oct, 09:13" icon="check" color="success">
        Card ending 4242.
    </x-gui::timeline.item>
    <x-gui::timeline.item title="Delayed at the depot" time="6 Oct, 14:40" icon="exclamation-triangle" color="warning">
        Bad weather in Leeds. We expect it to leave tomorrow.
    </x-gui::timeline.item>
    <x-gui::timeline.item title="Out for delivery" icon="arrow-right">
        Estimated by 6 PM.
    </x-gui::timeline.item>
</x-gui::timeline>
```

## Activity feed

Without an icon each event gets a small dot. The marker slot takes anything, such as an avatar, and the default slot takes any content.

```blade
<x-gui::timeline label="Activity" class="max-w-md">
    <x-gui::timeline.item time="2 hours ago">
        <x-slot:marker><x-gui::avatar name="Ada Lovelace" size="sm" /></x-slot:marker>
        <x-slot:title>Ada Lovelace commented</x-slot:title>
        <div class="rounded-gui border border-zinc-200 p-3 dark:border-zinc-800">Looks good to me. Ship it after the copy review.</div>
    </x-gui::timeline.item>
    <x-gui::timeline.item title="Grace Hopper changed the status to In review" time="Yesterday" color="primary" />
    <x-gui::timeline.item title="Alan Turing opened this" time="3 Oct" />
</x-gui::timeline>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `label` | `string` | `null` | The list's accessible name, on timeline. |
| `title` | `string` | `null` | What happened, on timeline.item. Also a slot, for markup. |
| `time, datetime` | `string \| DateTimeInterface, string` | `null` | When, as shown, and machine-readable. A date object fills in both. |
| `format` | `string` | `'j M Y, H:i'` | How a date object is shown. |
| `icon` | `string` | `null` | An icon in a circle as the marker. Without one, a small dot. |
| `color` | `'primary' \| 'secondary' \| 'success' \| 'warning' \| 'danger' \| 'info'` | `'secondary'` | The marker's colour. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `default` | More detail under the title: text, a card, anything. |
| `marker` | Your own marker, such as an avatar, instead of the icon or dot. |
| `title` | The title, when it needs markup. |
