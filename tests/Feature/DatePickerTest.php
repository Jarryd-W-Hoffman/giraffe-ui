<?php

it('renders a native date input with a calendar button and popover', function () {
    $html = (string) $this->blade('<x-gui::date-picker id="starts" name="starts_on" value="2026-10-05" min="2026-01-01" max="2026-12-31" />');

    expect(attr($html, 'input', 'type'))->toBe('date')
        ->and(attr($html, 'input', 'id'))->toBe('starts')
        ->and(attr($html, 'input', 'name'))->toBe('starts_on')
        ->and(attr($html, 'input', 'value'))->toBe('2026-10-05')
        ->and(attr($html, 'input', 'min'))->toBe('2026-01-01')
        ->and(attr($html, 'input', 'max'))->toBe('2026-12-31')
        ->and($html)
        ->toMatch('/<button(?=[^>]*commandfor="starts-calendar")(?=[^>]*command="toggle-popover")(?=[^>]*aria-label="Choose date")(?=[^>]*aria-haspopup="dialog")(?=[^>]*x-cloak)[^>]*>/s')
        ->toMatch('/<div(?=[^>]*id="starts-calendar")(?=[^>]*\spopover\s)(?=[^>]*role="dialog")(?=[^>]*aria-labelledby="starts-month")[^>]*>/s')
        ->toContain('<table role="grid" aria-labelledby="starts-month"', 'aria-label="Previous month"', 'aria-label="Next month"');
});

it('works inside a field', function () {
    withErrors(['starts_on' => 'Pick a date.']);

    $html = (string) $this->blade('<x-gui::field label="Start" name="starts_on" hint="Any weekday." required><x-gui::date-picker /></x-gui::field>');

    expect(attr($html, 'label', 'for'))->toBe('gui-starts-on')
        ->and(attr($html, 'input', 'id'))->toBe('gui-starts-on')
        ->and(attr($html, 'input', 'required'))->not->toBeNull()
        ->and(attr($html, 'input', 'aria-invalid'))->toBe('true')
        ->and(attr($html, 'input', 'aria-describedby'))->toBe('gui-starts-on-hint gui-starts-on-error')
        ->and($html)->toContain('commandfor="gui-starts-on-calendar"');
});

it('formats a date object and refills old input', function () {
    $html = (string) $this->blade('<x-gui::date-picker name="a" :value="$date" />', ['date' => new DateTimeImmutable('2026-02-03 14:00')]);

    expect(attr($html, 'input', 'value'))->toBe('2026-02-03');

    $this->app['session']->put('_old_input', ['b' => '2025-12-25']);
    $this->app['request']->setLaravelSession($this->app['session']->driver());
    $html = (string) $this->blade('<x-gui::date-picker name="b" />');

    expect(attr($html, 'input', 'value'))->toBe('2025-12-25');
});

it('puts wire:model on the input and leaves the value to Livewire', function () {
    $this->app['session']->put('_old_input', ['birthday' => '2000-01-01']);
    $this->app['request']->setLaravelSession($this->app['session']->driver());

    $html = (string) $this->blade('<x-gui::date-picker name="birthday" wire:model="birthday" />');

    expect($html)->toMatch('/<input(?=[^>]*type="date")(?=[^>]*wire:model="birthday")[^>]*>/s')
        ->and(attr($html, 'input', 'value'))->toBeNull();
});

it('disables the calendar button with the input', function () {
    $html = (string) $this->blade('<x-gui::date-picker aria-label="Date" disabled />');

    expect(attr($html, 'input', 'disabled'))->not->toBeNull()
        ->and($html)->toMatch('/<button(?=[^>]*aria-label="Choose date")(?=[^>]*disabled)[^>]*>/s');
});

it('generates an id when there is no name', function () {
    $html = (string) $this->blade('<x-gui::date-picker aria-label="Date" />');

    expect(attr($html, 'input', 'id'))->toMatch('/^gui-date-[a-z0-9]{8}$/');
});

it('takes a locale, a week start and translated labels', function () {
    $html = (string) $this->blade('<x-gui::date-picker aria-label="Fecha" locale="es_ES" week-start="8" button-label="Elegir fecha" previous-label="Mes anterior" next-label="Mes siguiente" />');

    expect($html)->toContain("locale: 'es-ES'", 'weekStart: 1,', 'aria-label="Elegir fecha"', 'aria-label="Mes anterior"', 'aria-label="Mes siguiente"');
});

it('follows the page locale and week by default', function () {
    $html = (string) $this->blade('<x-gui::date-picker aria-label="Date" />');

    expect($html)->toContain('locale: null,', 'weekStart: null,');
});

it('passes disabled dates and weekdays to the calendar', function () {
    $html = (string) $this->blade('<x-gui::date-picker :disabled-dates="[\'2026-12-25\', new DateTime(\'2027-01-01\')]" :disabled-weekdays="[0, 6, 7]" unavailable-message="Closed that day." />');

    expect($html)
        ->toContain("blockedDates: JSON.parse('[\\u00222026-12-25\\u0022,\\u00222027-01-01\\u0022]')")
        // 7 is Sunday again, so it's folded into 0.
        ->toContain("blockedWeekdays: JSON.parse('[0,6]')")
        ->toContain("'Closed that day.'", 'setCustomValidity');
});
