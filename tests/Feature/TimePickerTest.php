<?php

it('is a native time input with a button for the list of times', function () {
    $html = (string) $this->blade('<x-gui::field label="Start time" name="start_time"><x-gui::time-picker value="09:30" min="08:00" :interval="15" required /></x-gui::field>');

    expect($html)
        ->toMatch('/type="time"[^>]*id="gui-start-time"[^>]*name="start_time"/s')
        ->toContain('value="09:30"', 'min="08:00"', 'required="required"', 'interval: 15')
        ->toContain('aria-label="Choose time"', 'aria-haspopup="listbox"', 'commandfor="gui-start-time-times"', 'role="listbox"', 'popover');
});

it('takes a date object, and refills from old input', function () {
    expect((string) $this->blade('<x-gui::time-picker name="at" :value="new DateTime(\'2026-10-07 18:45:12\')" />'))->toContain('value="18:45"');

    session()->put('_old_input', ['at' => '07:15']);
    request()->setLaravelSession(session()->driver());

    expect((string) $this->blade('<x-gui::time-picker name="at" />'))->toContain('value="07:15"');
});

it('turns off its button with the input', function () {
    expect((string) $this->blade('<x-gui::time-picker name="at" disabled />'))->toMatch('/aria-label="Choose time"[^>]*disabled/s')
        ->and((string) $this->blade('<x-gui::time-picker name="at" readonly />'))->toMatch('/aria-label="Choose time"[^>]*disabled/s');
});

it('marks itself invalid from the error bag', function () {
    withErrors(['at' => 'Pick a time.']);

    expect((string) $this->blade('<x-gui::field label="At" name="at"><x-gui::time-picker /></x-gui::field>'))->toMatch('/type="time"[^>]*aria-invalid="true"/s');
});

it('splits a datetime into a named date and time', function () {
    $html = (string) $this->blade('<x-gui::field label="Starts" name="starts_at" hint="Local time."><x-gui::datetime-picker value="2026-10-07 14:30" min="2026-10-01" min-time="08:00" :disabled-weekdays="[0]" required /></x-gui::field>');

    expect($html)
        ->toContain('role="group"', 'aria-labelledby="gui-starts-at-label"')
        ->toMatch('/type="date"[^>]*id="gui-starts-at"[^>]*name="starts_at\[date\]"[^>]*aria-describedby="gui-starts-at-hint"[^>]*value="2026-10-07"[^>]*aria-label="Date"[^>]*min="2026-10-01"/s')
        ->toMatch('/type="time"[^>]*id="gui-starts-at-time"[^>]*name="starts_at\[time\]"[^>]*aria-describedby="gui-starts-at-hint"[^>]*value="14:30"[^>]*aria-label="Time"[^>]*min="08:00"/s')
        ->toContain('blockedWeekdays: JSON.parse')
        ->and(substr_count($html, 'required="required"'))->toBe(2);
});

it('binds each part with wire:model, and shows each part\'s error', function () {
    withErrors(['starts_at.time' => 'Pick a time.']);

    $html = (string) $this->blade('<x-gui::datetime-picker name="starts_at" wire:model="startsAt" />');

    expect($html)->toContain('wire:model="startsAt.date"', 'wire:model="startsAt.time"', 'id="gui-starts-at-time-error"', 'Pick a time.')
        ->toMatch('/type="time"[^>]*aria-describedby="gui-starts-at-time-error"/s');
});

it('takes a date object or an array as its value', function () {
    expect((string) $this->blade('<x-gui::datetime-picker name="at" :value="new DateTime(\'2026-01-02 03:04\')" />'))->toContain('value="2026-01-02"', 'value="03:04"')
        ->and((string) $this->blade('<x-gui::datetime-picker name="at" :value="[\'date\' => \'2026-05-06\', \'time\' => \'07:08\']" />'))->toContain('value="2026-05-06"', 'value="07:08"');
});
