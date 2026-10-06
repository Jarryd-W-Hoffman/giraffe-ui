<?php

it('copies a value with an icon button named Copy', function () {
    $html = (string) $this->blade('<x-gui::copy-button value="composer require jayaitch/giraffeui" />');

    expect($html)
        ->toContain('data-copy="composer require jayaitch/giraffeui"', 'aria-label="Copy"', 'x-cloak', 'role="status"')
        ->not->toContain('data-copy-target');
});

it('copies from a target, with a label that swaps to Copied', function () {
    $html = (string) $this->blade('<x-gui::copy-button target="#invite-link" copied-label="Link copied">Copy invite link</x-gui::copy-button>');

    expect($html)
        ->toContain('data-copy-target="#invite-link"', 'Copy invite link', 'Link copied')
        ->not->toContain('aria-label="Copy"');
});

it('escapes the text it copies', function () {
    expect((string) $this->blade('<x-gui::copy-button value="<script>alert(1)</script>" />'))
        ->toContain('data-copy="&lt;script&gt;alert(1)&lt;/script&gt;"')->not->toContain('<script>alert');
});

it('puts a copy button in a copyable input, aimed at that input', function () {
    $html = (string) $this->blade('<x-gui::field label="API key" name="api_key"><x-gui::input value="sk_123" readonly copyable /></x-gui::field>');

    expect($html)->toContain('id="gui-api-key"', 'data-copy-target="[id=&quot;gui-api-key&quot;]"', 'aria-label="Copy"', 'pe-9');
});

it('leaves a viewable password without a copy button', function () {
    expect((string) $this->blade('<x-gui::input type="password" name="password" viewable copyable />'))
        ->toContain('aria-label="Show password"')->not->toContain('data-copy-target');
});
