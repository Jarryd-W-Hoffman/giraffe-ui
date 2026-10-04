<?php

describe('modal', function () {
    it('renders a labelled native dialog', function () {
        $html = (string) $this->blade('<x-gui::modal id="confirm" title="Delete project?" description="This cannot be undone.">Body</x-gui::modal>');

        expect(attr($html, 'dialog', 'id'))->toBe('confirm')
            ->and(attr($html, 'dialog', 'aria-labelledby'))->toBe('confirm-title')
            ->and(attr($html, 'dialog', 'aria-describedby'))->toBe('confirm-description')
            ->and(attr($html, 'dialog', 'closedby'))->toBe('any')
            ->and(attr($html, 'dialog', 'wire:ignore.self'))->not->toBeNull()
            ->and($html)->toContain('<h2 id="confirm-title"', 'Body', 'commandfor="confirm"', 'command="close"', 'aria-label="Close"');
    });

    it('can be made non-dismissible', function () {
        $html = (string) $this->blade('<x-gui::modal id="m" title="Accept terms" :dismissible="false">Body</x-gui::modal>');

        expect(attr($html, 'dialog', 'closedby'))->toBe('none')
            ->and($html)->not->toContain('aria-label="Close"');
    });

    it('renders a footer and a close button that targets the modal', function () {
        $html = (string) $this->blade(<<<'BLADE'
            <x-gui::modal id="confirm" title="Sure?">
                Body
                <x-slot:footer>
                    <x-gui::card id="not-the-modal">
                        <x-gui::modal.close>Cancel</x-gui::modal.close>
                    </x-gui::card>
                </x-slot:footer>
            </x-gui::modal>
            BLADE);

        expect($html)->toMatch('/<button[^>]*commandfor="confirm"[^>]*command="close"[^>]*>\s*Cancel/s');
    });

    it('lets a close button target another modal', function () {
        $this->blade('<x-gui::modal.close for="other">Close</x-gui::modal.close>')
            ->assertSee('commandfor="other"', false);
    });

    it('opens on page load when asked', function () {
        expect((string) $this->blade('<x-gui::modal id="m" :open="true">Body</x-gui::modal>'))->toContain("document.getElementById('m')?.showModal()")
            ->and((string) $this->blade('<x-gui::modal id="m">Body</x-gui::modal>'))->not->toContain('<script');
    });

    it('escapes the id in the open script', function () {
        $html = (string) $this->blade('<x-gui::modal id="</script><script>alert(1)" :open="true">Body</x-gui::modal>');

        expect($html)->not->toContain('<script>alert(1)');
    });
});

describe('accordion', function () {
    it('renders details elements', function () {
        $html = (string) $this->blade(<<<'BLADE'
            <x-gui::accordion>
                <x-gui::accordion.item title="Shipping" open>Ships in 2 days.</x-gui::accordion.item>
                <x-gui::accordion.item title="Returns">30 days.</x-gui::accordion.item>
            </x-gui::accordion>
            BLADE);

        expect(substr_count($html, '<details'))->toBe(2)
            ->and($html)->toContain('<summary', 'Shipping', 'Ships in 2 days.')
            ->and($html)->not->toContain('name=');
        expect(attr($html, 'details', 'open'))->not->toBeNull();
    });

    it('shares a name between items when exclusive', function () {
        $html = (string) $this->blade(<<<'BLADE'
            <x-gui::accordion exclusive name="faq">
                <x-gui::accordion.item title="One">1</x-gui::accordion.item>
                <x-gui::accordion.item title="Two">2</x-gui::accordion.item>
            </x-gui::accordion>
            BLADE);

        expect(substr_count($html, 'name="faq"'))->toBe(2);
    });

    it('generates a name when exclusive without one', function () {
        $html = (string) $this->blade('<x-gui::accordion exclusive><x-gui::accordion.item title="One">1</x-gui::accordion.item></x-gui::accordion>');

        expect($html)->toMatch('/name="gui-accordion-[a-z0-9]{8}"/');
    });
});

describe('tabs', function () {
    beforeEach(function () {
        $this->html = (string) $this->blade(<<<'BLADE'
            <x-gui::tabs id="settings" :tabs="['account' => 'Account', 'password' => 'Password']">
                <x-gui::tabs.panel name="account">Account settings</x-gui::tabs.panel>
                <x-gui::tabs.panel name="password">Password settings</x-gui::tabs.panel>
            </x-gui::tabs>
            BLADE);
    });

    it('renders a tablist with linked tabs and panels', function () {
        expect($this->html)->toContain(
            'role="tablist"',
            'id="settings-tab-account"',
            'aria-controls="settings-panel-account"',
            'id="settings-panel-password"',
            'aria-labelledby="settings-tab-password"',
        );
    });

    it('selects the first tab by default, on the server', function () {
        expect($this->html)
            ->toMatch('/id="settings-tab-account"[^>]*aria-selected="true"[^>]*tabindex="0"/s')
            ->toMatch('/id="settings-tab-password"[^>]*aria-selected="false"[^>]*tabindex="-1"/s')
            ->toMatch('/id="settings-panel-password"[^>]*\shidden\s/s')
            ->not->toMatch('/id="settings-panel-account"[^>]*\shidden\s/s');
    });

    it('can select another tab', function () {
        $html = (string) $this->blade('<x-gui::tabs id="t" :tabs="[\'a\' => \'A\', \'b\' => \'B\']" selected="b"><x-gui::tabs.panel name="a">A</x-gui::tabs.panel><x-gui::tabs.panel name="b">B</x-gui::tabs.panel></x-gui::tabs>');

        expect($html)->toMatch('/id="t-tab-b"[^>]*aria-selected="true"/s')
            ->toMatch('/id="t-panel-a"[^>]*\shidden\s/s');
    });

    it('supports wire:model through x-modelable', function () {
        $html = (string) $this->blade('<x-gui::tabs :tabs="[\'a\' => \'A\']" wire:model="tab"><x-gui::tabs.panel name="a">A</x-gui::tabs.panel></x-gui::tabs>');

        expect($html)->toContain('x-modelable="selected"', 'wire:model="tab"');
    });
});
