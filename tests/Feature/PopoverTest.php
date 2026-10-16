<?php

use JayAitch\GiraffeUI\Support\Anchor;

describe('anchor', function () {
    it('makes valid anchor names', function () {
        expect(Anchor::name('user-menu'))->toBe('--gui-user-menu')
            ->and(Anchor::name('Project Actions!'))->toBe('--gui-project-actions')
            ->and(Anchor::name('!!!'))->toBe('--gui-anchor');
    });

    it('flips when there is no room', function () {
        expect(Anchor::position('bottom-end'))->toContain('[position-area:bottom_span-x-start]', '[position-try-fallbacks:flip-block,flip-inline]')
            ->and(Anchor::position('nonsense'))->toContain('[position-area:bottom_span-x-end]');
    });
});

describe('popover', function () {
    it('anchors a native popover to its trigger', function () {
        $html = (string) $this->blade('<x-gui::popover id="share" label="Share">Copy the link.</x-gui::popover>');

        expect($html)->toContain('style="anchor-name: --gui-share"', 'style="position-anchor: --gui-share"', 'Copy the link.')
            ->toMatch('/<button[^>]*commandfor="share"[^>]*command="toggle-popover"/s')
            ->toMatch('/<div\s+id="share"\s+popover\s/s');
    });

    it('uses a custom trigger', function () {
        $html = (string) $this->blade('<x-gui::popover id="p"><x-slot:trigger><a href="#" data-custom>Help</a></x-slot:trigger> Text</x-gui::popover>');

        expect($html)->toContain('data-custom')->not->toContain('command="toggle-popover"');
    });
});

describe('dropdown', function () {
    beforeEach(function () {
        $this->html = (string) $this->blade(<<<'BLADE'
            <x-gui::card id="not-the-menu">
                <x-gui::dropdown id="actions" label="Options" position="bottom-end">
                    <x-gui::dropdown.item wire:click="edit">Edit</x-gui::dropdown.item>
                    <x-gui::dropdown.item href="/settings">Settings</x-gui::dropdown.item>
                    <x-gui::dropdown.separator />
                    <x-gui::dropdown.item color="danger">Delete</x-gui::dropdown.item>
                    <x-gui::dropdown.item disabled>Archive</x-gui::dropdown.item>
                </x-gui::dropdown>
            </x-gui::card>
            BLADE);
    });

    it('renders a menu popover with a default trigger', function () {
        expect($this->html)->toMatch('/<div\s+id="actions"\s+popover\s+role="menu"/s')
            ->toMatch('/<button[^>]*commandfor="actions"[^>]*command="toggle-popover"[^>]*aria-haspopup="menu"/s')
            ->toContain('[position-area:bottom_span-x-start]');
    });

    it('closes the menu from button items, even inside another component with an id', function () {
        expect($this->html)->toMatch('/<button(?=[^>]*role="menuitem")(?=[^>]*commandfor="actions")(?=[^>]*command="hide-popover")(?=[^>]*wire:click="edit")[^>]*>/s');
    });

    it('renders link items, separators and danger items', function () {
        expect($this->html)->toMatch('/<a href="\/settings" role="menuitem"/')
            ->toContain('role="separator"', 'text-gui-danger-text');
    });

    it('lets an item use its own command', function () {
        $html = (string) $this->blade('<x-gui::dropdown id="m" label="M"><x-gui::dropdown.item commandfor="confirm" command="show-modal">Delete</x-gui::dropdown.item></x-gui::dropdown>');

        expect($html)->toMatch('/role="menuitem"[^>]*commandfor="confirm"[^>]*command="show-modal"/s')
            ->not->toContain('command="hide-popover"');
    });

    it('marks disabled items without making them close the menu', function () {
        expect($this->html)->toMatch('/<button(?=[^>]*role="menuitem")(?=[^>]*aria-disabled="true")(?![^>]*command=)[^>]*>\s*Archive/s');
    });
});

describe('tooltip', function () {
    it('renders a tooltip anchored to its trigger', function () {
        $html = (string) $this->blade('<x-gui::tooltip text="Copy to clipboard" id="copy-tip"><x-gui::button aria-label="Copy">C</x-gui::button></x-gui::tooltip>');

        expect($html)->toContain('role="tooltip"', 'id="copy-tip"', 'Copy to clipboard', 'anchor-name: --gui-copy-tip', 'position-anchor: --gui-copy-tip', '[position-area:top]')
            ->toContain("setAttribute('aria-describedby', 'copy-tip')");
    });

    it('escapes the text', function () {
        expect((string) $this->blade('<x-gui::tooltip text="<b>x</b>"><button>?</button></x-gui::tooltip>'))->not->toContain('<b>x</b>');
    });
});

describe('toasts', function () {
    it('renders a polite live region in the top layer', function () {
        $html = (string) $this->blade('<x-gui::toasts />');

        expect($html)->toContain('popover="manual"', 'role="status"', 'aria-live="polite"', 'x-on:gui-toast.window', 'window.GiraffeUI.toast');
    });

    it('raises the prefixed event from GiraffeUI.toast()', function () {
        expect((string) $this->blade('<x-gui::toasts />'))->toContain("new CustomEvent('gui-toast'");
    });

    it('still listens for the unprefixed toast event', function () {
        expect((string) $this->blade('<x-gui::toasts />'))->toContain('x-on:toast.window');
    });

    it('shows a toast flashed to the session', function () {
        session()->flash('gui-toast', ['message' => 'Profile saved', 'color' => 'success']);

        expect((string) $this->blade('<x-gui::toasts />'))->toContain('add(', 'Profile saved');
    });

    it('accepts a plain string flash', function () {
        session()->flash('gui-toast', 'Saved');

        expect((string) $this->blade('<x-gui::toasts />'))->toContain('Saved');
    });

    it('still shows a toast flashed under the unprefixed key', function () {
        session()->flash('toast', 'Legacy saved');

        expect((string) $this->blade('<x-gui::toasts />'))->toContain('Legacy saved');
    });
});
