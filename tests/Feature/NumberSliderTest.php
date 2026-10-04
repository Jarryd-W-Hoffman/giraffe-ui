<?php

describe('number input', function () {
    it('renders a native number input with stepper buttons pinned to it', function () {
        $html = (string) $this->blade('<x-gui::number-input name="qty" min="1" max="10" step="2" value="3" class="max-w-40" />');

        expect(attr($html, 'input', 'type'))->toBe('number')
            ->and(attr($html, 'input', 'id'))->toBe('gui-qty')
            ->and(attr($html, 'input', 'value'))->toBe('3')
            ->and(attr($html, 'input', 'step'))->toBe('2')
            ->and(classesOf($html, 'input'))->toContain('max-w-40', 'text-center')
            ->and(attr($html, 'input', 'style'))->toBe('anchor-name: --gui-gui-qty;')
            ->and($html)
            ->toMatch('/<button(?=[^>]*tabindex="-1")(?=[^>]*aria-label="Decrease")(?=[^>]*aria-controls="gui-qty")(?=[^>]*x-cloak)(?=[^>]*left: calc\(anchor\(left\) \+ 0px\))[^>]*>/s')
            ->toMatch('/<button(?=[^>]*aria-label="Increase")(?=[^>]*right: calc\(anchor\(right\) \+ 0px\))[^>]*>/s');
    });

    it('works inside a field and refills old input', function () {
        withErrors(['guests' => 'Too many.']);
        $this->app['session']->put('_old_input', ['guests' => '9']);
        $this->app['request']->setLaravelSession($this->app['session']->driver());

        $html = (string) $this->blade('<x-gui::field label="Guests" name="guests"><x-gui::number-input /></x-gui::field>');

        expect(attr($html, 'label', 'for'))->toBe('gui-guests')
            ->and(attr($html, 'input', 'value'))->toBe('9')
            ->and(attr($html, 'input', 'aria-invalid'))->toBe('true')
            ->and(attr($html, 'input', 'aria-describedby'))->toBe('gui-guests-error');
    });

    it('disables both buttons with the input and takes translated labels', function () {
        $html = (string) $this->blade('<x-gui::number-input aria-label="Qty" disabled decrement-label="Menos" increment-label="Más" />');

        expect(preg_match_all('/<button[^>]*\sdisabled[\s>]/s', $html))->toBe(2)
            ->and(attr($html, 'input', 'disabled'))->not->toBeNull()
            ->and($html)->toContain('aria-label="Menos"', 'aria-label="Más"');
    });
});

describe('slider', function () {
    it('renders a native range with a formatted output and value text', function () {
        $html = (string) $this->blade('<x-gui::slider name="focus" min="0" max="12" value="8" show-value suffix=" hours" />');

        expect(attr($html, 'input', 'type'))->toBe('range')
            ->and(attr($html, 'input', 'id'))->toBe('gui-focus')
            ->and(attr($html, 'input', 'value'))->toBe('8')
            ->and(attr($html, 'input', 'aria-valuetext'))->toBe('8 hours')
            ->and(classesOf($html, 'input'))->toContain('accent-gui-primary')
            ->and($html)->toMatch('/<output for="gui-focus"[^>]*>8 hours<\/output>/');
    });

    it('shows the midpoint when there is no value, like the browser', function () {
        $html = (string) $this->blade('<x-gui::slider aria-label="Volume" min="0" max="50" show-value />');

        expect($html)->toMatch('/<output[^>]*>25<\/output>/')
            ->and(attr($html, 'input', 'aria-valuetext'))->toBeNull();
    });

    it('turns marks into a datalist', function () {
        $html = (string) $this->blade('<x-gui::slider id="b" aria-label="Budget" :marks="[0, 500, 1000]" />');
        $labelled = (string) $this->blade('<x-gui::slider id="c" aria-label="Size" :marks="[0 => \'Small\', 50 => \'Large\']" />');

        expect(attr($html, 'input', 'list'))->toBe('b-marks')
            ->and($html)->toContain('<datalist id="b-marks">')->toMatch('/<option value="500"\s*><\/option>/')
            ->and($labelled)->toMatch('/<option value="50"\s+label="Large"\s*><\/option>/');
    });

    it('is plain markup without show-value or affixes', function () {
        $html = (string) $this->blade('<x-gui::slider aria-label="Volume" />');

        expect($html)->not->toContain('x-data')->not->toContain('<output');
    });
});

describe('pinned parts', function () {
    it('pins the combobox icon and list to the text input', function () {
        $html = (string) $this->blade('<x-gui::combobox id="c" :options="[\'a\' => \'A\']" aria-label="C" class="max-w-xs" />');

        expect($html)->toMatch('/role="combobox"[^>]*style="anchor-name: --gui-c;"/s')
            ->toContain('position-anchor: --gui-c; top: calc(anchor(top) + 0px)')
            ->not->toMatch('/<div\s+class="relative"\s+style="anchor-name/');
    });

    it('pins leading and trailing content to the input', function () {
        $html = (string) $this->blade('<x-gui::input id="q" aria-label="Q"><x-slot:leading>L</x-slot:leading><x-slot:trailing>T</x-slot:trailing></x-gui::input>');

        expect(attr($html, 'input', 'style'))->toBe('anchor-name: --gui-q;')
            ->and($html)->toContain('left: calc(anchor(left) + 0px)">L</div>', 'right: calc(anchor(right) + 0px)">T</div>');
    });

    it('leaves a plain input without anchor styles', function () {
        expect(attr((string) $this->blade('<x-gui::input name="q" />'), 'input', 'style'))->toBeNull();
    });
});
