<?php

namespace Workbench\App;

/**
 * Everything the catalog shows, in one place.
 *
 * Example code is rendered as-is with Blade, so the code you read is exactly the code that ran.
 */
class Catalog
{
    /**
     * Guide pages, in sidebar order.
     *
     * @return array<string, string>
     */
    public static function guides(): array
    {
        return [
            'introduction' => 'Introduction',
            'installation' => 'Installation',
            'theming' => 'Theming',
        ];
    }

    /**
     * Data available to every example.
     *
     * @return array<string, mixed>
     */
    public static function data(): array
    {
        return [
            'users' => [
                ['name' => 'Ada Lovelace', 'email' => 'ada@example.com', 'role' => 'Admin', 'team' => ['name' => 'Engines']],
                ['name' => 'Grace Hopper', 'email' => 'grace@example.com', 'role' => 'Editor', 'team' => ['name' => 'Compilers']],
                ['name' => 'Alan Turing', 'email' => 'alan@example.com', 'role' => 'Viewer', 'team' => ['name' => 'Codebreaking']],
                ['name' => 'Katherine Johnson', 'email' => 'katherine@example.com', 'role' => 'Editor', 'team' => ['name' => 'Trajectories']],
            ],
        ];
    }

    /**
     * Components, in sidebar order.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function components(): array
    {
        return [
            'button' => [
                'name' => 'Button',
                'description' => 'Displays a button, or a link that looks like one.',
                'usage' => '<x-gui::button>Save</x-gui::button>',
                'playground' => [
                    'template' => '<x-gui::button :variant="$variant" :color="$color" :size="$size" :href="$href ?: null" :external="$external" :disabled="$disabled">{{ $label }}</x-gui::button>',
                    'controls' => [
                        ['name' => 'variant', 'type' => 'select', 'options' => ['solid', 'outline', 'ghost'], 'default' => 'solid'],
                        ['name' => 'color', 'type' => 'select', 'options' => ['primary', 'secondary', 'success', 'warning', 'danger', 'info'], 'default' => 'primary'],
                        ['name' => 'size', 'type' => 'select', 'options' => ['sm', 'md', 'lg'], 'default' => 'md'],
                        ['name' => 'label', 'type' => 'text', 'default' => 'Button', 'slot' => true],
                        ['name' => 'href', 'type' => 'text', 'default' => ''],
                        ['name' => 'external', 'type' => 'boolean', 'default' => false],
                        ['name' => 'disabled', 'type' => 'boolean', 'default' => false],
                    ],
                ],
                'examples' => [
                    [
                        'title' => 'Variants',
                        'code' => <<<'BLADE'
                            <x-gui::button>Solid</x-gui::button>
                            <x-gui::button variant="outline">Outline</x-gui::button>
                            <x-gui::button variant="ghost">Ghost</x-gui::button>
                            BLADE,
                    ],
                    [
                        'title' => 'Colours',
                        'code' => <<<'BLADE'
                            <x-gui::button color="primary">Primary</x-gui::button>
                            <x-gui::button color="secondary">Secondary</x-gui::button>
                            <x-gui::button color="success">Success</x-gui::button>
                            <x-gui::button color="warning">Warning</x-gui::button>
                            <x-gui::button color="danger">Danger</x-gui::button>
                            <x-gui::button color="info">Info</x-gui::button>
                            BLADE,
                    ],
                    [
                        'title' => 'Outline colours',
                        'code' => <<<'BLADE'
                            <x-gui::button variant="outline" color="primary">Primary</x-gui::button>
                            <x-gui::button variant="outline" color="secondary">Secondary</x-gui::button>
                            <x-gui::button variant="outline" color="success">Success</x-gui::button>
                            <x-gui::button variant="outline" color="warning">Warning</x-gui::button>
                            <x-gui::button variant="outline" color="danger">Danger</x-gui::button>
                            <x-gui::button variant="outline" color="info">Info</x-gui::button>
                            BLADE,
                    ],
                    [
                        'title' => 'Sizes',
                        'code' => <<<'BLADE'
                            <x-gui::button size="sm">Small</x-gui::button>
                            <x-gui::button size="md">Medium</x-gui::button>
                            <x-gui::button size="lg">Large</x-gui::button>
                            BLADE,
                    ],
                    [
                        'title' => 'With icons',
                        'description' => 'Use the leading and trailing slots. Mark decorative icons aria-hidden.',
                        'code' => <<<'BLADE'
                            <x-gui::button>
                                <x-slot:leading>
                                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" /></svg>
                                </x-slot:leading>
                                New project
                            </x-gui::button>

                            <x-gui::button variant="outline">
                                Continue
                                <x-slot:trailing>
                                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" /></svg>
                                </x-slot:trailing>
                            </x-gui::button>
                            BLADE,
                    ],
                    [
                        'title' => 'Icon only',
                        'description' => 'Give icon-only buttons an aria-label so screen readers can name them.',
                        'code' => <<<'BLADE'
                            <x-gui::button variant="ghost" color="secondary" size="sm" aria-label="Close" class="w-8 px-0">
                                <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" /></svg>
                            </x-gui::button>
                            BLADE,
                    ],
                    [
                        'title' => 'As a link',
                        'description' => 'Pass an href to render an <a>. Add external to open in a new tab safely.',
                        'code' => <<<'BLADE'
                            <x-gui::button href="#">Internal link</x-gui::button>
                            <x-gui::button href="https://laravel.com" external variant="outline">Laravel docs</x-gui::button>
                            BLADE,
                    ],
                    [
                        'title' => 'Disabled',
                        'description' => 'Disabled links lose their href and are marked aria-disabled.',
                        'code' => <<<'BLADE'
                            <x-gui::button disabled>Disabled button</x-gui::button>
                            <x-gui::button href="#" disabled variant="outline">Disabled link</x-gui::button>
                            BLADE,
                    ],
                    [
                        'title' => 'Full width',
                        'code' => <<<'BLADE'
                            <x-gui::button class="w-full">Continue</x-gui::button>
                            BLADE,
                    ],
                    [
                        'title' => 'With Alpine',
                        'description' => 'Any attribute passes straight through, including x-on and wire:click.',
                        'code' => <<<'BLADE'
                            <div x-data="{ count: 0 }">
                                <x-gui::button x-on:click="count++" variant="outline">
                                    Clicked <span x-text="count">0</span> times
                                </x-gui::button>
                            </div>
                            BLADE,
                    ],
                ],
                'props' => [
                    ['variant', "'solid' | 'outline' | 'ghost'", "'solid'", 'Visual style.'],
                    ['color', "'primary' | 'secondary' | 'success' | 'warning' | 'danger' | 'info'", "'primary'", 'Theme colour. Unknown values fall back to primary.'],
                    ['size', "'sm' | 'md' | 'lg'", "'md'", 'Height, padding and text size.'],
                    ['href', 'string', 'null', 'Renders an <a> instead of a <button>.'],
                    ['external', 'bool', 'false', 'With href, opens in a new tab with rel="noopener noreferrer".'],
                    ['type', "'button' | 'submit' | 'reset'", "'button'", 'Ignored for links.'],
                    ['disabled', 'bool', 'false', 'Disables a button, or removes the href from a link and sets aria-disabled.'],
                ],
                'slots' => [
                    ['default', 'The label.'],
                    ['leading', 'Content before the label, usually an icon.'],
                    ['trailing', 'Content after the label.'],
                ],
            ],

            'table' => [
                'name' => 'Table',
                'description' => 'Displays rows of data with headings and an empty state.',
                'preview' => 'block',
                'usage' => '<x-gui::table :columns="[\'name\', \'email\']" :rows="$users" />',
                'examples' => [
                    [
                        'title' => 'Basic',
                        'description' => 'Pass a list of keys and they become headings automatically.',
                        'code' => <<<'BLADE'
                            <x-gui::table :columns="['name', 'email', 'role']" :rows="$users" />
                            BLADE,
                    ],
                    [
                        'title' => 'Custom labels and nested keys',
                        'description' => 'Use a key => label map. Keys are read with data_get, so team.name works.',
                        'code' => <<<'BLADE'
                            <x-gui::table
                                :columns="['name' => 'Full name', 'team.name' => 'Team', 'role' => 'Role']"
                                :rows="$users"
                            />
                            BLADE,
                    ],
                    [
                        'title' => 'Empty state',
                        'code' => <<<'BLADE'
                            <x-gui::table :columns="['name', 'email']" :rows="[]">
                                <x-slot:empty>No users yet. Invite your first teammate.</x-slot:empty>
                            </x-gui::table>
                            BLADE,
                    ],
                ],
                'props' => [
                    ['columns', 'array', '[]', "A list of keys (['name', 'email']) or a key => label map."],
                    ['rows', 'iterable', '[]', 'Arrays, objects, Eloquent models or a collection.'],
                ],
                'slots' => [
                    ['empty', 'Shown when there are no rows. Defaults to "No results."'],
                ],
            ],
        ];
    }
}
