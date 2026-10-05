# Date picker

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A native date input with a calendar. Follows the WAI-ARIA date picker dialog pattern.

```blade
<x-gui::date-picker name="starts_on" />
```

## In a field

Type the date, or press the calendar button. In the calendar, arrow keys move by day and week, Home and End go to the start and end of the week, Page Up and Page Down change the month (with Shift, the year), Enter picks and Escape closes.

```blade
<x-gui::field label="Start date" name="starts_on" hint="The first day of the project.">
    <x-gui::date-picker value="2026-10-05" />
</x-gui::field>
```

## Earliest and latest dates

min and max are the native attributes, so the browser validates them too. Days outside them are crossed out and can't be picked.

```blade
<x-gui::field label="Delivery date" name="delivers_on">
    <x-gui::date-picker min="2026-10-07" max="2026-10-28" />
</x-gui::field>
```

## Date range

With range, there are two dates under one name: the form submits stay[start] and stay[end], and errors at stay.start and stay.end show under the control. In the calendar the first pick is the start and the second the end, with the days between highlighted as you point or move. The end can't be before the start, and the browser checks that too.

```blade
<x-gui::field label="Stay" name="stay" hint="Check-in and check-out.">
    <x-gui::date-picker range :value="['start' => '2026-10-12', 'end' => '2026-10-16']" min="2026-10-05" />
</x-gui::field>
```

## Disabled dates and weekdays

disabled-dates and disabled-weekdays (0 for Sunday to 6 for Saturday) can't be picked in the calendar, and a typed one fails the browser's validation with unavailable-message. Validate them on the server too.

```blade
<x-gui::field label="Collection date" name="collects_on" hint="Weekdays only, and not on public holidays.">
    <x-gui::date-picker value="2026-12-23" :disabled-weekdays="[0, 6]" :disabled-dates="['2026-12-25', '2026-12-28', '2027-01-01']" />
</x-gui::field>
```

## Locale and first day of the week

Month and day names come from the page's lang attribute, and the week starts where that locale starts it. Both can be set.

```blade
<x-gui::field label="Fecha" name="fecha">
    <x-gui::date-picker locale="es" week-start="1" button-label="Elegir fecha" previous-label="Mes anterior" next-label="Mes siguiente" />
</x-gui::field>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `size` | `'sm' \| 'md' \| 'lg'` | `'md'` |  |
| `invalid` | `bool \| null` | `null` | Force the invalid state. |
| `locale` | `string \| null` | `null` | For month and day names. Defaults to the page's lang attribute. |
| `week-start` | `int \| null` | `null` | 0 for Sunday to 6 for Saturday. Defaults to the locale's. |
| `button-label` | `string` | `'Choose date'` | The calendar button's accessible name. 'Choose dates' for a range. |
| `range` | `bool` | `false` | Pick a start and an end: name[start] and name[end]. value takes ['start' =&gt; …, 'end' =&gt; …]; wire:model="stay" binds stay.start and stay.end. |
| `start-label` | `string` | `'Start date'` | In a range, the first input's accessible name. |
| `end-label` | `string` | `'End date'` | In a range, the second input's accessible name. |
| `previous-label` | `string` | `'Previous month'` |  |
| `next-label` | `string` | `'Next month'` |  |
| `disabled-dates` | `array` | `[]` | Y-m-d strings or DateTimeInterface objects that can't be picked. |
| `disabled-weekdays` | `array` | `[]` | Weekdays that can't be picked: 0 for Sunday to 6 for Saturday. |
| `unavailable-message` | `string` | `'That date isn\'t available.'` | The browser's validation message for a typed disabled date. |

The value is the input's own: Y-m-d, from value, a DateTimeInterface or old input. wire:model, x-model, min, max and required go on the input.

Without JavaScript it is a plain date input with the browser's own picker. The calendar needs Alpine, which Livewire includes.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
