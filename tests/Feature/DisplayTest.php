<?php

describe('badge', function () {
    it('defaults to a soft secondary badge', function () {
        $html = (string) $this->blade('<x-gui::badge>Draft</x-gui::badge>');

        expect(classesOf($html, 'span'))->toContain('bg-gui-secondary/10', 'text-gui-secondary-text')
            ->and($html)->toContain('Draft');
    });

    it('applies variants and colours', function (string $variant, string $color, string $expected) {
        $html = (string) $this->blade("<x-gui::badge variant=\"{$variant}\" color=\"{$color}\">x</x-gui::badge>");

        expect(classesOf($html, 'span'))->toContain($expected);
    })->with([
        ['solid', 'success', 'bg-gui-success'],
        ['outline', 'danger', 'ring-gui-danger-text'],
        ['soft', 'warning', 'text-gui-warning-text'],
    ]);
});

describe('alert', function () {
    it('renders a title, body and default icon', function () {
        $html = (string) $this->blade('<x-gui::alert color="danger" title="Payment failed">Your card was declined.</x-gui::alert>');

        expect($html)->toContain('Payment failed', 'Your card was declined.', '<svg', 'text-gui-danger-text', 'aria-hidden="true"');
    });

    it('can hide or replace the icon', function () {
        $custom = (string) $this->blade('<x-gui::alert><x-slot:icon><i data-custom></i></x-slot:icon> Hi</x-gui::alert>');

        expect((string) $this->blade('<x-gui::alert :icon="false">Hi</x-gui::alert>'))->not->toContain('<svg')
            ->and($custom)->toContain('data-custom')->not->toContain('<svg');
    });

    it('renders actions', function () {
        $this->blade('<x-gui::alert>Hi<x-slot:actions><a href="/retry">Retry</a></x-slot:actions></x-gui::alert>')
            ->assertSee('<a href="/retry">Retry</a>', false);
    });
});

describe('card', function () {
    it('renders a header, body and footer', function () {
        $html = (string) $this->blade('<x-gui::card title="Team" description="Manage members.">Body <x-slot:footer>Footer</x-slot:footer></x-gui::card>');

        expect($html)->toContain('<h3', 'Team', 'Manage members.', 'Body', 'Footer');
    });

    it('skips the header when there is nothing to show', function () {
        expect((string) $this->blade('<x-gui::card>Body</x-gui::card>'))->not->toContain('<h3');
    });
});

describe('avatar', function () {
    it('renders an image with alt text', function () {
        $html = (string) $this->blade('<x-gui::avatar src="/ada.jpg" name="Ada Lovelace" />');

        expect(attr($html, 'img', 'src'))->toBe('/ada.jpg')
            ->and(attr($html, 'img', 'alt'))->toBe('Ada Lovelace');
    });

    it('falls back to initials with an accessible name', function () {
        $html = (string) $this->blade('<x-gui::avatar name="ada lovelace byron" />');

        expect($html)->toContain('role="img"', 'aria-label="ada lovelace byron"', '>AL</span>');
    });

    it('handles multibyte names', function () {
        expect((string) $this->blade('<x-gui::avatar name="Émile Ångström" />'))->toContain('>ÉÅ</span>');
    });

    it('is hidden from screen readers with no name', function () {
        $html = (string) $this->blade('<x-gui::avatar />');

        expect($html)->toContain('aria-hidden="true"', '<svg')->not->toContain('role="img"');
    });
});

describe('separator', function () {
    it('renders an hr by default', function () {
        expect((string) $this->blade('<x-gui::separator />'))->toContain('<hr');
    });

    it('renders a labelled separator', function () {
        expect((string) $this->blade('<x-gui::separator label="or" />'))->toContain('or')->not->toContain('<hr');
    });

    it('renders a vertical separator', function () {
        expect((string) $this->blade('<x-gui::separator orientation="vertical" />'))->toContain('role="separator"', 'aria-orientation="vertical"');
    });
});
