<?php

namespace Workbench\App\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Workbench\App\Catalog;

/**
 * Writes docs/components/*.md from the catalog, so the Markdown docs and the catalog never disagree.
 */
class GenerateDocs extends Command
{
    protected $signature = 'catalog:docs';

    protected $description = 'Generate the Markdown component docs from the catalog.';

    public function handle(Filesystem $files): int
    {
        $directory = __DIR__.'/../../../docs/components';

        $files->ensureDirectoryExists($directory);

        // Remove pages for components that no longer exist.
        foreach ($files->glob("{$directory}/*.md") as $path) {
            $files->delete($path);
        }

        $index = ["# Components\n", '_Generated from `workbench/app/Catalog.php` by `composer docs`. Run `composer catalog` to try them live._'];

        foreach (collect(Catalog::components())->groupBy('group', preserveKeys: true) as $group => $components) {
            $index[] = "\n## {$group}\n";

            foreach ($components as $slug => $component) {
                $files->put("{$directory}/{$slug}.md", $this->page($component));
                $index[] = "- [{$component['name']}]({$slug}.md): {$component['description']}";
            }
        }

        $files->put("{$directory}/README.md", implode("\n", $index)."\n");

        $this->components->info('Docs written to docs/components.');

        return self::SUCCESS;
    }

    /**
     * @param  array<string, mixed>  $component
     */
    protected function page(array $component): string
    {
        $lines = [
            "# {$component['name']}",
            '',
            '_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._',
            '',
            $component['description'],
            '',
            '```blade',
            $component['usage'],
            '```',
        ];

        foreach ($component['examples'] as $example) {
            array_push($lines, '', "## {$example['title']}");

            if (isset($example['description'])) {
                array_push($lines, '', $example['description']);
            }

            array_push($lines, '', '```blade', $example['code'], '```');
        }

        array_push($lines, '', '## Props', '', '| Prop | Type | Default | Description |', '|---|---|---|---|');

        foreach ($component['props'] as [$name, $type, $default, $description]) {
            $lines[] = '| `'.$name.'` | `'.str_replace('|', '\|', $type).'` | `'.$default.'` | '.str_replace('|', '\|', $description).' |';
        }

        array_push($lines, '', 'Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element.');

        if ($component['slots'] !== []) {
            array_push($lines, '', '## Slots', '', '| Slot | Description |', '|---|---|');

            foreach ($component['slots'] as [$name, $description]) {
                $lines[] = "| `{$name}` | {$description} |";
            }
        }

        return implode("\n", $lines)."\n";
    }
}
