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

describe('avatar group', function () {
    $people = [['name' => 'Ada Lovelace'], ['name' => 'Grace Hopper', 'src' => '/grace.jpg'], ['name' => 'Alan Turing'], ['name' => 'Katherine Johnson']];

    it('shows up to max and counts the rest', function () use ($people) {
        $html = (string) $this->blade('<x-gui::avatar.group :avatars="$people" :max="2" label="Team" size="sm" />', ['people' => $people]);

        expect(attr($html, 'div', 'role'))->toBe('group')
            ->and(attr($html, 'div', 'aria-label'))->toBe('Team')
            ->and($html)->toContain('aria-label="Ada Lovelace"', 'alt="Grace Hopper"', 'aria-label="2 more"', '>+2</span>')
            ->not->toContain('Alan Turing')
            ->and(substr_count($html, 'size-8'))->toBe(3)
            ->and(classesOf($html, 'div'))->toContain('-space-x-1')->not->toContain('-space-x-2');
    });

    it('adds more to the slot and translates the count', function () {
        $html = (string) $this->blade('<x-gui::avatar.group more="12" more-label=":count más"><x-gui::avatar name="Ada Lovelace" /></x-gui::avatar.group>');

        expect($html)->toContain('aria-label="Ada Lovelace"', 'aria-label="12 más"', '+12')
            ->and(attr($html, 'div', 'role'))->toBeNull();
    });

    it('has no count when everyone fits', function () use ($people) {
        $html = (string) $this->blade('<x-gui::avatar.group :avatars="$people" />', ['people' => $people]);

        expect($html)->not->toContain('more')
            ->and(substr_count($html, 'role="img"'))->toBe(3);
    });
});

describe('description list', function () {
    it('renders terms and escaped details from an array, then the slot', function () {
        $html = (string) $this->blade('<x-gui::description-list :items="[\'Name\' => \'Ada\', \'Note\' => \'<b>x</b>\']" class="mt-6"><x-gui::description-list.item term="Status"><em>Paid</em></x-gui::description-list.item></x-gui::description-list>');

        expect(classesOf($html, 'dl'))->toContain('mt-6', 'divide-y')
            ->and($html)->toMatch('/<dt[^>]*>Name<\/dt>\s*<dd[^>]*>Ada<\/dd>/')
            ->toContain('&lt;b&gt;x&lt;/b&gt;', '<em>Paid</em>')
            ->and(substr_count($html, '<dt'))->toBe(3)
            ->and(strpos($html, 'Status'))->toBeGreaterThan(strpos($html, 'Note'));
    });
});

describe('stat', function () {
    it('shows a rise as good, with an arrow and hidden text', function () {
        $html = (string) $this->blade('<x-gui::stat label="Revenue" value="$48,200" change="12%" trend="up" description="vs last month" />');

        expect($html)->toContain('Revenue', '$48,200', '<span class="sr-only">Increased by</span>', 'vs last month', 'text-gui-success-text')
            ->not->toContain('text-gui-danger-text');
    });

    it('inverts the colour when down is good', function () {
        $up = (string) $this->blade('<x-gui::stat label="Churn" value="2%" change="1 pt" trend="up" invert />');
        $down = (string) $this->blade('<x-gui::stat label="Latency" value="90 ms" change="10 ms" trend="down" invert decrease-label="Bajó" />');

        expect($up)->toContain('text-gui-danger-text', 'Increased by')
            ->and($down)->toContain('text-gui-success-text', '<span class="sr-only">Bajó</span>');
    });

    it('is neutral without a trend and plain without a change', function () {
        $neutral = (string) $this->blade('<x-gui::stat label="Users" value="1,024" change="0" />');
        $plain = (string) $this->blade('<x-gui::stat label="Users" value="1,024" />');

        expect($neutral)->not->toContain('sr-only')->not->toContain('text-gui-success-text')
            ->and($plain)->toMatch('/1,024<\/p>\s*<\/div>/');
    });
});

it('gives a badge a pill shape and a dot', function () {
    expect((string) $this->blade('<x-gui::badge shape="pill" dot="success">Paid</x-gui::badge>'))->toContain('rounded-full', 'bg-gui-success', 'aria-hidden="true"', 'Paid')
        ->and((string) $this->blade('<x-gui::badge dot="var(--color-lime-500)">Hosting</x-gui::badge>'))->toContain('style="background: var(--color-lime-500)"');
});

it('has a filled card with an icon before its title', function () {
    $html = (string) $this->blade('<x-gui::card variant="filled" title="Goals" icon="trophy">Body</x-gui::card>');

    expect($html)->toContain('data-variant="filled"', 'bg-zinc-100', 'rounded-[calc(var(--radius-gui)+1.125rem)]', '<svg')->not->toContain('shadow-xs');
});
