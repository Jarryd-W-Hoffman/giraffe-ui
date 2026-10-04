<?php

describe('drawer', function () {
    it('is a labelled native dialog that slides in from the right', function () {
        $html = (string) $this->blade('<x-gui::drawer id="cart" title="Your cart" description="2 items">Items</x-gui::drawer>');

        expect(attr($html, 'dialog', 'id'))->toBe('cart')
            ->and(attr($html, 'dialog', 'aria-labelledby'))->toBe('cart-title')
            ->and(attr($html, 'dialog', 'aria-describedby'))->toBe('cart-description')
            ->and(classesOf($html, 'dialog'))->toContain('ml-auto', 'h-dvh', 'translate-x-full', 'open:translate-x-0', 'border-l', 'max-w-md')
            ->and($html)->toContain('commandfor="cart"', 'command="close"');
    });

    it('can slide in from the left', function () {
        $html = (string) $this->blade('<x-gui::drawer id="nav" side="left">Menu</x-gui::drawer>');

        expect(classesOf($html, 'dialog'))->toContain('mr-auto', '-translate-x-full', 'border-r')->not->toContain('ml-auto');
    });

    it('lets modal.close target the drawer', function () {
        $html = (string) $this->blade('<x-gui::drawer id="cart" title="Cart">Items <x-slot:footer><x-gui::modal.close>Keep shopping</x-gui::modal.close></x-slot:footer></x-gui::drawer>');

        expect($html)->toMatch('/<button(?=[^>]*commandfor="cart")(?=[^>]*command="close")[^>]*>\s*Keep shopping/s');
    });

    it('leaves modals centred', function () {
        expect(classesOf((string) $this->blade('<x-gui::modal id="m">Body</x-gui::modal>'), 'dialog'))
            ->toContain('m-auto', 'scale-95', 'rounded-gui')->not->toContain('h-dvh');
    });
});

describe('progress', function () {
    it('reports its value to assistive technology', function () {
        $html = (string) $this->blade('<x-gui::progress :value="30" :max="120" label="Uploading" show-value />');

        expect($html)->toContain('role="progressbar"', 'aria-label="Uploading"', 'aria-valuemin="0"', 'aria-valuemax="100"', 'aria-valuenow="25"', 'width: 25%', '>25%<');
    });

    it('clamps out-of-range values', function () {
        expect((string) $this->blade('<x-gui::progress :value="250" label="x" />'))->toContain('aria-valuenow="100"')
            ->and((string) $this->blade('<x-gui::progress :value="-5" label="x" />'))->toContain('aria-valuenow="0"');
    });

    it('is indeterminate without a value', function () {
        $html = (string) $this->blade('<x-gui::progress label="Loading" />');

        expect($html)->toContain('animate-[gui-indeterminate', 'motion-reduce:animate-none')->not->toContain('aria-valuenow');
    });

    it('uses theme colours', function () {
        expect((string) $this->blade('<x-gui::progress :value="10" color="success" label="x" />'))->toContain('bg-gui-success');
    });
});

it('renders keyboard keys', function () {
    expect((string) $this->blade('<x-gui::kbd>⌘</x-gui::kbd><x-gui::kbd>K</x-gui::kbd>'))->toContain('<kbd', '>⌘</kbd>', '>K</kbd>');
});
