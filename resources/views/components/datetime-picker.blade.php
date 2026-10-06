@aware(['guiField' => null])

@props([
    'size' => 'md',
    'invalid' => null,
    'value' => null,
    'interval' => 30,
    'locale' => null,
    'weekStart' => null,
    'disabledDates' => [],
    'disabledWeekdays' => [],
    'dateLabel' => null,
    'timeLabel' => null,
])

@php
    $dateLabel ??= __('giraffeui::ui.date');
    $timeLabel ??= __('giraffeui::ui.time');

    $control = \JayAitch\GiraffeUI\Support\Form::control($guiField, $attributes, $errors ?? null, $invalid);
    $id = $control['attributes']['id'] ?? \JayAitch\GiraffeUI\Support\Id::make('gui-datetime');
    $base = $control['name'];

    // The value as a date and a time: a date object, "2026-10-07 14:30" (or with a T), or ['date' => …, 'time' => …].
    $value = match (true) {
        $value instanceof \DateTimeInterface => ['date' => $value->format('Y-m-d'), 'time' => $value->format('H:i')],
        is_string($value) && $value !== '' => array_combine(['date', 'time'], array_pad(array_slice(preg_split('/[ T]/', trim($value)), 0, 2), 2, null)),
        default => (array) ($value ?? []),
    };

    // A date and a time under one name: name[date] and name[time], with errors and old input at name.date and
    // name.time. wire:model="startsAt" or x-model="startsAt" binds startsAt.date and startsAt.time.
    $binding = $attributes->whereStartsWith(['wire:model', 'x-model']);
    $parts = [];
    foreach (['date', 'time'] as $part) {
        $errorKey = $base ? \JayAitch\GiraffeUI\Support\Form::errorKey($base).'.'.$part : null;
        $message = \JayAitch\GiraffeUI\Support\Form::error($errorKey, null, $errors ?? null);
        $partId = $part === 'date' ? $id : "{$id}-time";

        $parts[$part] = [
            'id' => $partId,
            'name' => $base ? "{$base}[{$part}]" : null,
            'value' => $value[$part] ?? ($binding->isEmpty() && $errorKey ? old($errorKey) : null),
            'label' => $part === 'date' ? $dateLabel : $timeLabel,
            'binding' => collect($binding->getAttributes())->mapWithKeys(fn ($model, $key) => [$key => "{$model}.{$part}"])->all(),
            'message' => $message,
            'invalid' => $control['invalid'] || $message !== null,
            // The field's hint and error describe both parts; each part's own error describes it too.
            'describedBy' => \JayAitch\GiraffeUI\Support\Form::describedBy($control['attributes']['aria-describedby'] ?? null, $message ? "{$partId}-error" : null),
        ];
    }

    // required, disabled and the like apply to both; min and max (dates) to the date, and min-time/max-time to the time.
    $shared = $attributes->only(['required', 'disabled', 'readonly', 'form'])
        ->merge(isset($control['attributes']['required']) ? ['required' => true] : []);
    $dateOnly = $attributes->only(['min', 'max']);
    $timeOnly = collect(['min-time' => 'min', 'max-time' => 'max'])
        ->filter(fn ($to, $from) => $attributes->has($from))
        ->mapWithKeys(fn ($to, $from) => [$to => $attributes->get($from)])
        ->all();
    $dateAttributes = $shared->merge($dateOnly->getAttributes())->merge($parts['date']['binding']);
    $timeAttributes = $shared->merge($timeOnly)->merge($parts['time']['binding']);
    $outer = $attributes->except(['required', 'disabled', 'readonly', 'form', 'min', 'max', 'min-time', 'max-time', 'value', 'name', 'id', 'aria-describedby', ...array_keys($binding->getAttributes())]);
@endphp

{{--
    A date and a time side by side: a Date picker and a Time picker, named as one. Without JavaScript they're two native
    inputs. Combine them on the server, e.g. Carbon::parse($request->input('starts_at.date').' '.$request->input('starts_at.time')).
--}}
<div
    role="group"
    @if ($guiField && ($guiField['label'] ?? false)) aria-labelledby="{{ $id }}-label" @endif
    {{ $outer->class('flex w-full min-w-0 gap-2') }}
>
    <div class="min-w-0 flex-1">
        <x-gui::date-picker
            :gui-field="null"
            :size="$size"
            :locale="$locale"
            :week-start="$weekStart"
            :disabled-dates="$disabledDates"
            :disabled-weekdays="$disabledWeekdays"
            :invalid="$parts['date']['invalid']"
            :id="$parts['date']['id']"
            :name="$parts['date']['name']"
            :value="$parts['date']['value']"
            :aria-label="$parts['date']['label']"
            :aria-describedby="$parts['date']['describedBy']"
            :attributes="$dateAttributes"
        />
    </div>
    <div class="w-40 shrink-0">
        <x-gui::time-picker
            :gui-field="null"
            :size="$size"
            :locale="$locale"
            :interval="$interval"
            :invalid="$parts['time']['invalid']"
            :id="$parts['time']['id']"
            :name="$parts['time']['name']"
            :value="$parts['time']['value']"
            :aria-label="$parts['time']['label']"
            :aria-describedby="$parts['time']['describedBy']"
            :attributes="$timeAttributes"
        />
    </div>
</div>

@foreach ($parts as $part)
    @if ($part['message'])
        <p id="{{ $part['id'] }}-error" class="mt-1.5 text-sm font-medium text-gui-danger-text">{{ $part['message'] }}</p>
    @endif
@endforeach
