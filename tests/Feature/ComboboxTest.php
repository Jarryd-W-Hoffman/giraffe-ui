<?php

$countries = ['au' => 'Australia', 'nz' => 'New Zealand', 'gb' => 'United Kingdom'];

it('renders an ARIA combobox linked to its listbox', function () use ($countries) {
    $html = (string) $this->blade('<x-gui::combobox id="country" name="country" :options="$countries" placeholder="Search" />', ['countries' => $countries]);

    expect($html)
        ->toMatch('/<input(?=[^>]*type="text")(?=[^>]*role="combobox")(?=[^>]*aria-controls="country-listbox")(?=[^>]*aria-expanded="false")(?=[^>]*aria-autocomplete="list")(?=[^>]*id="country")(?![^>]*\sname=)[^>]*>/s')
        ->toContain('<ul id="country-listbox" role="listbox"', 'popover="manual"', 'placeholder="Search"')
        ->and(substr_count($html, 'role="option"'))->toBe(3);
});

it('submits the value through a hidden input', function () use ($countries) {
    $html = (string) $this->blade('<x-gui::combobox name="country" :options="$countries" value="nz" />', ['countries' => $countries]);

    expect($html)->toMatch('/<input type="hidden" name="country"[^>]*value="nz"/')
        ->toMatch('/data-value="nz"\s+data-label="New Zealand"\s+aria-selected="true"/')
        ->toMatch('/data-value="au"\s+data-label="Australia"\s+aria-selected="false"/');
});

it('works inside a field', function () use ($countries) {
    withErrors(['country' => 'Pick a country.']);

    $html = (string) $this->blade('<x-gui::field label="Country" name="country" hint="Where you live."><x-gui::combobox :options="$countries" /></x-gui::field>', ['countries' => $countries]);

    expect($html)
        ->toMatch('/<label[^>]*for="gui-country"/')
        ->toMatch('/<input(?=[^>]*role="combobox")(?=[^>]*id="gui-country")(?=[^>]*aria-invalid="true")(?=[^>]*aria-describedby="gui-country-hint gui-country-error")[^>]*>/s')
        ->toMatch('/<input type="hidden" name="country"/');
});

it('binds wire:model on the root through x-modelable', function () use ($countries) {
    $html = (string) $this->blade('<x-gui::combobox wire:model.live="country" :options="$countries" aria-label="Country" />', ['countries' => $countries]);

    expect($html)->toMatch('/x-modelable="value"[^>]*wire:model.live="country"/s')
        ->toMatch('/role="combobox"[^>]*aria-label="Country"|aria-label="Country"[^>]*role="combobox"/s')
        ->not->toMatch('/role="combobox"[^>]*wire:model/s');
});

it('escapes option labels', function () {
    $html = (string) $this->blade('<x-gui::combobox :options="[\'x\' => \'<b>bold</b>\']" aria-label="X" />');

    expect($html)->not->toContain('<b>bold</b>');
});
