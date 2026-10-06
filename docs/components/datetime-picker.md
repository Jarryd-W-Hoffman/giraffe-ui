# Datetime picker

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A date and a time side by side, named as one: a Date picker and a Time picker that submit name[date] and name[time].

```blade
<x-gui::datetime-picker name="starts_at" />
```

## Basic

The field's label names the pair; each part is also named Date or Time. Takes a date object, "2026-10-07 14:30" or ['date' =&gt; …, 'time' =&gt; …] as its value.

```blade
<x-gui::field label="Starts" name="starts_at" hint="In your local time." class="max-w-md">
    <x-gui::datetime-picker value="2026-10-07 14:30" />
</x-gui::field>
```

## Limits

min and max limit the date; min-time and max-time limit the time, every day. interval spaces the list of times.

```blade
<x-gui::field label="Delivery slot" name="delivery" class="max-w-md">
    <x-gui::datetime-picker min="2026-10-08" max="2026-10-31" min-time="08:00" max-time="20:00" :interval="60" :disabled-weekdays="[0]" required />
</x-gui::field>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `value` | `string \| DateTimeInterface \| array` | `null` | A date object, "Y-m-d H:i", or ['date' =&gt; …, 'time' =&gt; …]. Old input refills each part. |
| `min, max` | `string` | `null` | The earliest and latest dates, as Y-m-d. |
| `min-time, max-time` | `string` | `null` | The earliest and latest times of day, as H:i. |
| `interval` | `int` | `30` | Minutes between the times in the list. |
| `locale, week-start` | `string, int` | `null` | As on the Date and Time pickers. |
| `disabled-dates, disabled-weekdays` | `array` | `[]` | Dates and weekdays that can't be picked, as on the Date picker. |
| `date-label, time-label` | `string` | `'Date', 'Time'` | Each part's accessible name (translatable). |
| `size` | `'sm' \| 'md' \| 'lg'` | `'md'` |  |

It submits name[date] (Y-m-d) and name[time] (H:i). Validate starts_at.date and starts_at.time, and combine them with Carbon::parse($request-&gt;input('starts_at.date').' '.$request-&gt;input('starts_at.time')).

wire:model="startsAt" and x-model="startsAt" bind startsAt.date and startsAt.time. Errors at starts_at.date and starts_at.time show under the pair.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
