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

it('searches on the server with url, starting from options, without Livewire', function () use ($countries) {
    $html = (string) $this->blade('<x-gui::combobox name="country" url="/countries" :options="$countries" value="nz" :debounce="150" />', ['countries' => $countries]);

    // Options are rendered by Alpine from the server's results, starting with the given ones.
    expect($html)
        ->toContain('<template x-for="(option, index) in results"', 'x-bind:aria-busy="searching"', "url: '\\/countries'")
        // Initial options live in a script tag, not x-data, so a Livewire re-render can't reset the component.
        ->toContain('<script type="application/json" x-ref="initial">[{"value":"au","label":"Australia"},{"value":"nz","label":"New Zealand"},{"value":"gb","label":"United Kingdom"}]</script>', "selectedLabel: 'New Zealand'", "query === '' ? 0 : 150)")
        ->not->toContain('data-value="au"', '$wire')
        ->and(substr_count($html, 'role="option"'))->toBe(1);
});

it('filters in the browser when there is no url', function () use ($countries) {
    $html = (string) $this->blade('<x-gui::combobox :options="$countries" />', ['countries' => $countries]);

    expect($html)->toContain('url: null', 'data-value="au"')->not->toContain('<template x-for');
});

it("escapes labels so they can't close the initial options' script tag", function () {
    $html = (string) $this->blade('<x-gui::combobox url="/x" :options="[\'a\' => \'</script><b>x</b>\']" />');

    expect($html)->not->toContain('</script><b>')->toContain('\u003C/script\u003E\u003Cb\u003Ex\u003C/b\u003E');
});

describe('multiple', function () use ($countries) {
    it('submits name[] and marks every chosen option', function () use ($countries) {
        $html = (string) $this->blade('<x-gui::field label="Countries" name="countries"><x-gui::combobox multiple :options="$countries" :value="[\'au\', \'gb\']" /></x-gui::field>', ['countries' => $countries]);

        expect($html)
            ->toContain('name="countries[]" x-bind:value="chosen"', 'aria-multiselectable="true"', 'multiple: true', 'Remove :label')
            ->toMatch('/data-value="au"\s+data-label="Australia"\s+aria-selected="true"/')
            ->toMatch('/data-value="nz"\s+data-label="New Zealand"\s+aria-selected="false"/')
            ->toMatch('/data-value="gb"\s+data-label="United Kingdom"\s+aria-selected="true"/')
            ->toContain("value: JSON.parse('[\\u0022au\\u0022,\\u0022gb\\u0022]')");
    });

    it('refills from old input and binds an array with wire:model', function () use ($countries) {
        session()->put('_old_input', ['countries' => ['nz']]);
        request()->setLaravelSession(session()->driver());

        expect((string) $this->blade('<x-gui::combobox multiple name="countries" :options="$countries" />', ['countries' => $countries]))
            ->toMatch('/data-value="nz"\s+data-label="New Zealand"\s+aria-selected="true"/');

        expect((string) $this->blade('<x-gui::combobox multiple wire:model="countries" :options="$countries" />', ['countries' => $countries]))
            ->toContain('wire:model="countries"')->toMatch("/value: (\\[\\]|JSON\\.parse\\('\\[\\]'\\)),/")->not->toContain('$wire');
    });

    it('accepts a single value as a list of one', function () use ($countries) {
        expect((string) $this->blade('<x-gui::combobox multiple :options="$countries" value="nz" />', ['countries' => $countries]))
            ->toContain("value: JSON.parse('[\\u0022nz\\u0022]')");
    });
});
