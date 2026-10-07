<?php

it('announces with a badge, a sentence and an arrow, as one link', function () {
    $html = (string) $this->blade('<x-gui::announcement href="/changelog">Charts are here</x-gui::announcement>');

    expect($html)->toMatch('/<a href="\/changelog"/')->toContain('New<span class="sr-only">:</span>', 'Charts are here', 'rounded-full')
        ->and((string) $this->blade('<x-gui::announcement href="#" :badge="false">Plain</x-gui::announcement>'))->not->toContain('sr-only">:');
});

it('shows a snippet to copy, with a prompt that is not copied', function () {
    $html = (string) $this->blade('<x-gui::snippet value="composer require jayaitch/giraffeui" />');

    expect($html)->toContain('<code class="truncate">composer require jayaitch/giraffeui</code>', 'data-copy="composer require jayaitch/giraffeui"', '>$</span>')
        ->and((string) $this->blade('<x-gui::snippet value="npm i" :prompt="false" />'))->not->toContain('select-none');
});

it('draws a code block with a copy button, static or live', function () {
    expect((string) $this->blade('<x-gui::code-block code="<x-gui::button />" />'))->toContain('&lt;x-gui::button /&gt;', '>Copy</button>', 'role="status"', 'tabindex="0"')
        ->and((string) $this->blade('<x-gui::code-block live="css" />'))->toContain('x-text="css"');
});

it('picks a colour from swatches, as named native radios', function () {
    $html = (string) $this->blade('<x-gui::swatches name="accent" label="Accent" value="b" x-model="accent" :options="[\'a\' => \'red\', \'b\' => [\'label\' => \'Blue\', \'color\' => \'blue\']]" />');

    expect($html)->toContain('<legend', 'Accent', '<span class="sr-only">A</span>', '<span class="sr-only">Blue</span>', 'style="background: blue"')
        ->and(substr_count($html, 'x-model="accent"'))->toBe(2)
        ->and($html)->toMatch('/value="b"\s+checked/');
});

it('lays out a feature, a page preview, a backdrop and a footer', function () {
    expect((string) $this->blade('<x-gui::feature icon="bolt" title="Fast" heading="h2">Quick.</x-gui::feature>'))->toContain('<h2 class="text-lg mt-6">Fast</h2>', 'Quick.')
        ->and((string) $this->blade('<x-gui::page-preview href="/x" title="X" description="An example." />'))->toContain('<iframe src="/x" title="X" loading="lazy" inert tabindex="-1"', 'An example.')
        ->and((string) $this->blade('<x-gui::backdrop />'))->toContain('aria-hidden="true"', 'blur-3xl');

    $footer = (string) $this->blade('<x-gui::footer description="About." :links="[\'Docs\' => [\'Install\' => \'/install\']]"><x-slot:brand>Acme</x-slot:brand><x-slot:legal>© Acme</x-slot:legal></x-gui::footer>');
    expect($footer)->toContain('<footer', '<nav aria-label="Docs">', 'href="/install"', 'About.', '© Acme');
});

it('shows a big display stat, and triggers a command palette', function () {
    expect((string) $this->blade('<x-gui::stat variant="display" value="61" label="components" />'))->toContain('text-center', 'text-5xl', '61', 'components')
        ->and((string) $this->blade('<x-gui::command-palette.trigger for="search">Search</x-gui::command-palette.trigger>'))->toContain('commandfor="search"', 'command="show-modal"', 'aria-keyshortcuts="Meta+K Control+K"')
        ->and((string) $this->blade('<x-gui::command-palette.trigger variant="panel" description="Anything.">Find</x-gui::command-palette.trigger>'))->toContain('Anything.', 'p-6');
});

it('comes in variants: backdrops, footers, features and announcements', function () {
    expect((string) $this->blade('<x-gui::backdrop variant="dots" />'))->toContain('radial-gradient(var(--color-zinc-300)_1px')->not->toContain('blur-3xl')
        ->and((string) $this->blade('<x-gui::backdrop variant="aurora" />'))->toContain('bg-sky-300/30')
        ->and((string) $this->blade('<x-gui::backdrop variant="nonsense" />'))->toContain('blur-3xl', 'bg-size-[3.5rem_3.5rem]');

    $links = "['Docs' => ['Install' => '/install'], 'Legal' => ['Terms' => '/terms']]";
    expect((string) $this->blade('<x-gui::footer variant="simple" :links="'.$links.'" />'))->toContain('<nav aria-label="Footer">', 'href="/install"', 'href="/terms"', 'py-8')
        ->and((string) $this->blade('<x-gui::footer variant="centered" :links="'.$links.'"><x-slot:social><a href="#" aria-label="RSS">r</a></x-slot:social></x-gui::footer>'))->toContain('text-center', 'aria-label="RSS"')
        ->and((string) $this->blade('<x-gui::footer variant="newsletter" newsletter-action="/subscribe" :links="'.$links.'" />'))->toContain('action="/subscribe"', 'type="email"', 'name="email"', 'Subscribe', 'name="_token"');

    expect((string) $this->blade('<x-gui::feature variant="inline" icon="bolt" title="Fast">Quick.</x-gui::feature>'))->toContain('flex gap-4', 'bg-gui-primary/10')->not->toContain('rounded-3xl')
        ->and((string) $this->blade('<x-gui::announcement href="#" variant="subtle">Out now</x-gui::announcement>'))->not->toContain('backdrop-blur');
});
