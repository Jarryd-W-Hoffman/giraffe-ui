<?php

describe('stepper', function () {
    it('marks steps before the current one complete', function () {
        $html = (string) $this->blade('<x-gui::stepper :steps="[\'account\' => \'Account\', \'shipping\' => \'Shipping\', \'review\' => \'Review\']" current="shipping" />');

        expect($html)->toContain('aria-label="Progress"', '<ol data-orientation="horizontal"')
            ->toMatch('/Account<span class="sr-only">, completed<\/span>/')
            ->toMatch('/aria-current="step"[^>]*>\s*<span aria-hidden="true"[^>]*ring-gui-primary-text/')
            ->and(substr_count($html, '<li'))->toBe(3)
            ->and(substr_count($html, 'aria-current'))->toBe(1)
            ->and(substr_count($html, ', completed'))->toBe(1)
            ->and(substr_count($html, '<svg'))->toBe(1);
    });

    it('starts on the first step and accepts list keys as strings', function () {
        $first = (string) $this->blade('<x-gui::stepper :steps="[\'One\', \'Two\']" />');
        $second = (string) $this->blade('<x-gui::stepper :steps="[\'One\', \'Two\']" current="1" />');

        expect($first)->toMatch('/aria-current="step"[^>]*>(?:(?!<\/div>).)*One/s')
            ->and($second)->toMatch('/aria-current="step"[^>]*>(?:(?!<\/div>).)*Two/s')
            ->toContain('One<span class="sr-only">, completed');
    });

    it('can mark every step complete', function () {
        $html = (string) $this->blade('<x-gui::stepper :steps="[\'One\', \'Two\']" complete />');

        expect(substr_count($html, ', completed'))->toBe(2)
            ->and($html)->not->toContain('aria-current');
    });

    it('links back to earlier steps only', function () {
        $html = (string) $this->blade('<x-gui::stepper :steps="[
            \'a\' => [\'label\' => \'Account\', \'href\' => \'/a\'],
            \'b\' => [\'label\' => \'Shipping\', \'href\' => \'/b\', \'description\' => \'Where it goes\'],
            \'c\' => [\'label\' => \'Review\', \'href\' => \'/c\'],
        ]" current="b" />');

        expect($html)->toContain('href="/a"', 'Where it goes')
            ->not->toContain('href="/b"', 'href="/c"');
    });

    it('flags steps with errors', function () {
        $html = (string) $this->blade('<x-gui::stepper :steps="[\'a\' => \'Account\', \'b\' => \'Shipping\']" current="b" :invalid="[\'a\', \'b\']" />');

        expect(substr_count($html, ', needs attention'))->toBe(2)
            ->and(substr_count($html, 'bg-gui-danger '))->toBe(2)
            ->and($html)->toContain('aria-current="step"');
    });

    it('lays out vertically', function () {
        expect((string) $this->blade('<x-gui::stepper :steps="[\'One\']" orientation="vertical" />'))
            ->toContain('data-orientation="vertical"', 'flex flex-col');
    });

    it('can be composed, with click handlers as buttons', function () {
        $html = (string) $this->blade('<x-gui::stepper aria-label="Checkout"><x-gui::stepper.step status="complete" wire:click="goTo(1)">Cart</x-gui::stepper.step><x-gui::stepper.step status="current">Pay</x-gui::stepper.step></x-gui::stepper>');

        expect($html)->toContain('aria-label="Checkout"')->not->toContain('aria-label="Progress"')
            ->toMatch('/<button\s+type="button"[^>]*wire:click="goTo\(1\)"/')
            ->toMatch('/<div\s+aria-current="step"[^>]*>/');
    });

    it('escapes labels', function () {
        expect((string) $this->blade('<x-gui::stepper :steps="[\'<b>x</b>\']" />'))
            ->toContain('&lt;b&gt;x&lt;/b&gt;');
    });
});
