<?php

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use JayAitch\GiraffeUI\GiraffeUIServiceProvider;

function translateTo(string $locale, array $lines): void
{
    app('translator')->addLines(collect($lines)->mapWithKeys(fn ($line, $key) => ["ui.{$key}" => $line])->all(), $locale, 'giraffeui');
    app()->setLocale($locale);
}

it('has every key the components use, and uses every key it has', function () {
    $sources = collect(File::allFiles(__DIR__.'/../../resources/views'))
        ->merge(File::allFiles(__DIR__.'/../../src'))
        ->map(fn ($file) => $file->getContents())
        ->implode("\n");

    preg_match_all("/giraffeui::ui\.([a-z_]+)/", $sources, $matches);
    $used = collect($matches[1])->unique()->sort()->values()->all();
    $defined = collect(array_keys(require __DIR__.'/../../lang/en/ui.php'))->sort()->values()->all();

    expect(array_values(array_diff($used, $defined)))->toBe([], 'used but not defined')
        ->and(array_values(array_diff($defined, $used)))->toBe([], 'defined but not used');
});

it('shows built-in text in the app\'s language', function () {
    translateTo('fr', ['close' => 'Fermer', 'choose_date' => 'Choisir une date', 'previous_month' => 'Mois précédent', 'no_results' => 'Aucun résultat']);

    expect((string) $this->blade('<x-gui::modal id="m" title="T">x</x-gui::modal>'))->toContain('aria-label="Fermer"')
        ->and((string) $this->blade('<x-gui::date-picker aria-label="Date" />'))->toContain('aria-label="Choisir une date"', 'aria-label="Mois précédent"')
        ->and((string) $this->blade('<x-gui::combobox :options="[\'a\' => \'A\']" />'))->toContain('Aucun résultat');
});

it('lets props win over translations', function () {
    translateTo('fr', ['choose_date' => 'Choisir une date']);

    expect((string) $this->blade('<x-gui::date-picker aria-label="Date" button-label="Pick" />'))
        ->toContain('aria-label="Pick"')->not->toContain('Choisir une date');
});

it('translates the pagination sentence and escapes it', function () {
    translateTo('fr', ['showing' => '<b>De</b> :first à :last sur :total', 'previous' => 'Précédent', 'next' => 'Suivant']);

    $paginator = new LengthAwarePaginator(range(1, 10), 30, 10, 2, ['path' => '/users']);
    $html = (string) $this->blade('<x-gui::pagination :paginator="$paginator" />', ['paginator' => $paginator]);

    expect($html)->toContain('&lt;b&gt;De&lt;/b&gt;', '>11</span> à <span', '>30</span>', 'Précédent', 'Suivant')
        ->not->toContain('<b>De</b>');
});

it('fills in counts', function () {
    translateTo('fr', ['characters' => ':count caractères', 'up_to_characters' => 'Jusqu’à :max caractères.', 'more' => ':count de plus']);

    expect((string) $this->blade('<x-gui::textarea aria-label="Notes" counter>abc</x-gui::textarea>'))->toContain('3 caractères')
        ->and((string) $this->blade('<x-gui::textarea aria-label="Notes" counter maxlength="20" />'))->toContain('Jusqu’à 20 caractères.')
        ->and((string) $this->blade('<x-gui::avatar.group :avatars="[[\'name\' => \'A\'], [\'name\' => \'B\'], [\'name\' => \'C\']]" :max="1" />'))->toContain('aria-label="2 de plus"');
});

it('publishes its language files', function () {
    $paths = ServiceProvider::pathsToPublish(GiraffeUIServiceProvider::class, 'giraffeui-lang');

    expect(array_map('realpath', array_keys($paths)))->toBe([realpath(__DIR__.'/../../lang')])
        ->and(array_values($paths))->toBe([lang_path('vendor/giraffeui')]);
});
