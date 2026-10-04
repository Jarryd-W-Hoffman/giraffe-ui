<?php

/**
 * Get the class names on the first matching element, as an array.
 */
function classesOf(string $html, string $tag): array
{
    preg_match('/<'.$tag.'\b[^>]*\bclass="([^"]*)"/s', $html, $matches);

    return preg_split('/\s+/', trim($matches[1] ?? ''), -1, PREG_SPLIT_NO_EMPTY);
}

it('renders a button with type="button" by default', function () {
    $this->blade('<x-gui::button>Save</x-gui::button>')
        ->assertSee('<button', false)
        ->assertSee('type="button"', false)
        ->assertSee('Save')
        ->assertDontSee('onclick', false);
});

it('accepts a custom type', function () {
    $this->blade('<x-gui::button type="submit">Save</x-gui::button>')
        ->assertSee('type="submit"', false);
});

it('applies the full class names for each variant and colour', function (string $variant, string $color, array $expected) {
    $html = (string) $this->blade("<x-gui::button variant=\"{$variant}\" color=\"{$color}\">Go</x-gui::button>");

    expect(classesOf($html, 'button'))->toContain(...$expected);
})->with([
    ['solid', 'primary', ['bg-gui-primary', 'text-gui-primary-foreground']],
    ['solid', 'warning', ['bg-gui-warning', 'text-gui-warning-foreground']],
    ['outline', 'danger', ['border', 'border-gui-danger-text', 'text-gui-danger-text']],
    ['ghost', 'success', ['text-gui-success-text', 'hover:bg-gui-success/10']],
]);

it('falls back to solid primary for unknown variants and colours', function () {
    $html = (string) $this->blade('<x-gui::button variant="nope" color="nope">Go</x-gui::button>');

    expect(classesOf($html, 'button'))->toContain('bg-gui-primary');
});

it('applies size classes', function (string $size, string $expected) {
    $html = (string) $this->blade("<x-gui::button size=\"{$size}\">Go</x-gui::button>");

    expect(classesOf($html, 'button'))->toContain($expected);
})->with([
    ['sm', 'h-8'],
    ['md', 'h-10'],
    ['lg', 'h-12'],
]);

it('keeps every class separate when extra classes are merged in', function () {
    $html = (string) $this->blade('<x-gui::button disabled class="w-full">Go</x-gui::button>');

    expect(classesOf($html, 'button'))->toContain('text-sm', 'w-full', 'disabled:opacity-50');
});

it('passes other attributes straight through', function () {
    $this->blade('<x-gui::button wire:click="save" x-on:click="open = true" data-test="btn">Go</x-gui::button>')
        ->assertSee('wire:click="save"', false)
        ->assertSee('x-on:click="open = true"', false)
        ->assertSee('data-test="btn"', false);
});

it('disables a button', function () {
    $pattern = '/<button\b[^>]*\sdisabled(\s|>)/';

    expect((string) $this->blade('<x-gui::button disabled>Go</x-gui::button>'))->toMatch($pattern);
    expect((string) $this->blade('<x-gui::button>Go</x-gui::button>'))->not->toMatch($pattern);
});

it('renders a link when given an href', function () {
    $html = (string) $this->blade('<x-gui::button href="/dashboard">Go</x-gui::button>');

    expect($html)
        ->toContain('<a', 'href="/dashboard"')
        ->not->toContain('<button', 'type=', 'target=');
});

it('opens external links in a new tab safely', function () {
    $this->blade('<x-gui::button href="https://example.com" external>Go</x-gui::button>')
        ->assertSee('target="_blank"', false)
        ->assertSee('rel="noopener noreferrer"', false);
});

it('removes the href from a disabled link and marks it aria-disabled', function () {
    $html = (string) $this->blade('<x-gui::button href="/dashboard" disabled>Go</x-gui::button>');

    expect($html)
        ->toContain('aria-disabled="true"', 'role="link"')
        ->not->toContain('href=');
});

it('escapes the href', function () {
    $this->blade('<x-gui::button href="{{ $url }}">Go</x-gui::button>', ['url' => '"><script>alert(1)</script>'])
        ->assertDontSee('<script>', false);
});

it('renders leading and trailing slots around the label', function () {
    $this->blade(<<<'BLADE'
        <x-gui::button>
            <x-slot:leading><svg data-icon="left"></svg></x-slot:leading>
            Next
            <x-slot:trailing><svg data-icon="right"></svg></x-slot:trailing>
        </x-gui::button>
        BLADE)
        ->assertSeeInOrder(['data-icon="left"', 'Next', 'data-icon="right"'], false);
});
