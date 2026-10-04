<?php

namespace Workbench\App;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

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
            'livewire' => 'Livewire',
        ];
    }

    /**
     * Data available to every example.
     *
     * @return array<string, mixed>
     */
    public static function data(): array
    {
        $sort = in_array(request('sort'), ['name', 'email', 'role'], true) ? request('sort') : null;
        $direction = request('direction') === 'desc' ? 'desc' : 'asc';

        $people = collect(range(1, 47))->map(fn (int $i) => [
            'id' => $i,
            'name' => ['Ada', 'Grace', 'Alan', 'Katherine', 'Margaret', 'Edsger', 'Barbara', 'Donald'][$i % 8].' #'.$i,
            'email' => 'user'.$i.'@example.com',
            'role' => ['Admin', 'Editor', 'Viewer'][$i % 3],
        ]);

        return [
            'roles' => ['admin' => 'Admin', 'editor' => 'Editor', 'viewer' => 'Viewer'],
            'countries' => self::countries(),
            // A self-contained "photo" so the catalog never depends on an image host.
            'photo' => 'data:image/svg+xml,'.rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 96 96"><defs><linearGradient id="g" x2="1" y2="1"><stop offset="0" stop-color="#f472b6"/><stop offset="1" stop-color="#6366f1"/></linearGradient></defs><rect width="96" height="96" fill="url(#g)"/><circle cx="48" cy="38" r="16" fill="#fff" fill-opacity=".85"/><path d="M16 96c4-20 18-30 32-30s28 10 32 30z" fill="#fff" fill-opacity=".85"/></svg>'),
            'sort' => $sort,
            'direction' => $direction,
            'sortedUsers' => collect(self::users())->when($sort, fn ($users) => $users->sortBy($sort, SORT_NATURAL, $direction === 'desc'))->values()->all(),
            'paginated' => new LengthAwarePaginator(
                $people->forPage(Paginator::resolveCurrentPage(), 8)->values(),
                $people->count(),
                8,
                null,
                ['path' => Paginator::resolveCurrentPath()],
            ),
            'users' => self::users(),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function users(): array
    {
        return [
            ['name' => 'Ada Lovelace', 'email' => 'ada@example.com', 'role' => 'Admin', 'team' => ['name' => 'Engines']],
            ['name' => 'Grace Hopper', 'email' => 'grace@example.com', 'role' => 'Editor', 'team' => ['name' => 'Compilers']],
            ['name' => 'Alan Turing', 'email' => 'alan@example.com', 'role' => 'Viewer', 'team' => ['name' => 'Codebreaking']],
            ['name' => 'Katherine Johnson', 'email' => 'katherine@example.com', 'role' => 'Editor', 'team' => ['name' => 'Trajectories']],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function countries(): array
    {
        return [
            'ar' => 'Argentina', 'au' => 'Australia', 'at' => 'Austria', 'be' => 'Belgium', 'br' => 'Brazil',
            'ca' => 'Canada', 'cl' => 'Chile', 'cn' => 'China', 'dk' => 'Denmark', 'eg' => 'Egypt',
            'fi' => 'Finland', 'fr' => 'France', 'de' => 'Germany', 'gr' => 'Greece', 'in' => 'India',
            'id' => 'Indonesia', 'ie' => 'Ireland', 'it' => 'Italy', 'jp' => 'Japan', 'ke' => 'Kenya',
            'mx' => 'Mexico', 'nl' => 'Netherlands', 'nz' => 'New Zealand', 'ng' => 'Nigeria', 'no' => 'Norway',
            'pt' => 'Portugal', 'za' => 'South Africa', 'kr' => 'South Korea', 'es' => 'Spain', 'se' => 'Sweden',
            'ch' => 'Switzerland', 'gb' => 'United Kingdom', 'us' => 'United States', 'vn' => 'Vietnam',
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
                'group' => 'Actions',
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
                            <x-gui::button variant="ghost" color="secondary" size="sm" aria-label="Close" class="w-8 px-0!">
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
                        'title' => 'Loading',
                        'description' => 'With Livewire this is automatic: a button with wire:click, or a submit button in a wire:submit form, shows a spinner and ignores clicks while its request runs. Set loading yourself otherwise.',
                        'code' => <<<'BLADE'
                            <x-gui::button loading>Saving</x-gui::button>
                            <x-gui::button loading variant="outline">
                                <x-slot:leading>
                                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" /></svg>
                                </x-slot:leading>
                                Creating
                            </x-gui::button>
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
                    ['loading', 'bool', 'false', 'Shows a spinner in place of the leading icon and disables the button. Automatic for Livewire actions.'],
                ],
                'slots' => [
                    ['default', 'The label.'],
                    ['leading', 'Content before the label, usually an icon.'],
                    ['trailing', 'Content after the label.'],
                ],
            ],

            'field' => [
                'name' => 'Field',
                'group' => 'Forms',
                'description' => 'Wraps a control with a label, hint and error message, and links them for screen readers.',
                'preview' => 'narrow',
                'usage' => <<<'BLADE'
                    <x-gui::field label="Email" name="email" hint="We'll never share it.">
                        <x-gui::input type="email" />
                    </x-gui::field>
                    BLADE,
                'examples' => [
                    [
                        'title' => 'Label and hint',
                        'description' => 'The control gets its id and name from the field, and aria-describedby points at the hint.',
                        'code' => <<<'BLADE'
                            <x-gui::field label="Email" name="email" hint="We'll never share it.">
                                <x-gui::input type="email" placeholder="you@example.com" />
                            </x-gui::field>
                            BLADE,
                    ],
                    [
                        'title' => 'Validation errors',
                        'description' => 'Errors come from Laravel\'s $errors bag using the field name, or pass one with error. The control is marked aria-invalid.',
                        'code' => <<<'BLADE'
                            <x-gui::field label="Username" name="username" error="That username is taken.">
                                <x-gui::input value="ada" />
                            </x-gui::field>
                            BLADE,
                    ],
                    [
                        'title' => 'Required',
                        'code' => <<<'BLADE'
                            <x-gui::field label="Full name" name="name" required>
                                <x-gui::input />
                            </x-gui::field>
                            BLADE,
                    ],
                    [
                        'title' => 'With Livewire',
                        'description' => 'Put wire:model on the control and name on the field so errors are found.',
                        'code' => <<<'BLADE'
                            <x-gui::field label="Bio" name="bio">
                                <x-gui::textarea wire:model="bio" />
                            </x-gui::field>
                            BLADE,
                    ],
                ],
                'props' => [
                    ['label', 'string', 'null', 'Label text, linked to the control.'],
                    ['name', 'string', 'null', 'Passed to the control and used to find errors. Array names like address[city] work.'],
                    ['id', 'string', 'gui-{name}', 'Id for the control. Derived from name by default.'],
                    ['hint', 'string', 'null', 'Help text shown under the control.'],
                    ['error', 'string', 'null', 'An error message. Defaults to the first error for name.'],
                    ['required', 'bool', 'false', 'Marks the label and makes the control required.'],
                ],
                'slots' => [
                    ['default', 'The control: input, textarea or select.'],
                ],
            ],

            'input' => [
                'name' => 'Input',
                'group' => 'Forms',
                'description' => 'A text input that works on its own or inside a field.',
                'preview' => 'narrow',
                'usage' => '<x-gui::input name="email" type="email" />',
                'playground' => [
                    'selfClosing' => true,
                    'template' => '<x-gui::input :type="$type" :size="$size" :placeholder="$placeholder ?: null" :disabled="$disabled" :invalid="$invalid ?: null" class="max-w-xs" />',
                    'controls' => [
                        ['name' => 'type', 'type' => 'select', 'options' => ['text', 'email', 'password', 'number', 'search', 'date', 'file'], 'default' => 'text'],
                        ['name' => 'size', 'type' => 'select', 'options' => ['sm', 'md', 'lg'], 'default' => 'md'],
                        ['name' => 'placeholder', 'type' => 'text', 'default' => 'Type here…', 'always' => true],
                        ['name' => 'disabled', 'type' => 'boolean', 'default' => false],
                        ['name' => 'invalid', 'type' => 'boolean', 'default' => false],
                    ],
                ],
                'examples' => [
                    [
                        'title' => 'Sizes',
                        'code' => <<<'BLADE'
                            <x-gui::input size="sm" placeholder="Small" />
                            <x-gui::input size="md" placeholder="Medium" />
                            <x-gui::input size="lg" placeholder="Large" />
                            BLADE,
                    ],
                    [
                        'title' => 'With icons',
                        'description' => 'Leading content is decorative. Trailing content can be interactive, like a button.',
                        'code' => <<<'BLADE'
                            <x-gui::input type="search" placeholder="Search" aria-label="Search">
                                <x-slot:leading>
                                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" /></svg>
                                </x-slot:leading>
                            </x-gui::input>

                            <div x-data="{ show: false }">
                                <x-gui::input x-bind:type="show ? 'text' : 'password'" value="hunter2" aria-label="Password">
                                    <x-slot:trailing>
                                        <button type="button" class="text-xs font-medium hover:text-zinc-900 dark:hover:text-zinc-100" x-on:click="show = ! show" x-text="show ? 'Hide' : 'Show'">Show</button>
                                    </x-slot:trailing>
                                </x-gui::input>
                            </div>
                            BLADE,
                    ],
                    [
                        'title' => 'Disabled',
                        'code' => <<<'BLADE'
                            <x-gui::input value="Can't touch this" disabled aria-label="Disabled example" />
                            BLADE,
                    ],
                    [
                        'title' => 'File',
                        'code' => <<<'BLADE'
                            <x-gui::input type="file" aria-label="Upload a file" />
                            BLADE,
                    ],
                ],
                'props' => [
                    ['type', 'string', "'text'", 'Any input type.'],
                    ['size', "'sm' | 'md' | 'lg'", "'md'", 'Height and text size.'],
                    ['invalid', 'bool | null', 'null', 'Force the invalid state. By default it follows the error bag.'],
                ],
                'slots' => [
                    ['leading', 'Content inside the input, before the text. Usually an icon.'],
                    ['trailing', 'Content inside the input, after the text. Can be interactive.'],
                ],
            ],

            'textarea' => [
                'name' => 'Textarea',
                'group' => 'Forms',
                'description' => 'A multi-line text input.',
                'preview' => 'narrow',
                'usage' => '<x-gui::textarea name="bio" rows="4" />',
                'examples' => [
                    [
                        'title' => 'In a field',
                        'code' => <<<'BLADE'
                            <x-gui::field label="Bio" name="bio" hint="A sentence or two about you.">
                                <x-gui::textarea rows="4" placeholder="I build things…" />
                            </x-gui::field>
                            BLADE,
                    ],
                    [
                        'title' => 'With content',
                        'code' => <<<'BLADE'
                            <x-gui::textarea aria-label="Notes">Remember the milk.</x-gui::textarea>
                            BLADE,
                    ],
                ],
                'props' => [
                    ['rows', 'int', '3', 'Visible lines of text.'],
                    ['invalid', 'bool | null', 'null', 'Force the invalid state.'],
                ],
                'slots' => [
                    ['default', 'The initial text. Defaults to old input.'],
                ],
            ],

            'select' => [
                'name' => 'Select',
                'group' => 'Forms',
                'description' => 'A native select, styled to match the other controls.',
                'preview' => 'narrow',
                'usage' => '<x-gui::select name="role" :options="$roles" placeholder="Choose a role" />',
                'examples' => [
                    [
                        'title' => 'Options from an array',
                        'description' => 'Pass value => label pairs. The value prop (or old input) is selected.',
                        'code' => <<<'BLADE'
                            <x-gui::field label="Role" name="role">
                                <x-gui::select :options="$roles" placeholder="Choose a role" />
                            </x-gui::field>
                            BLADE,
                    ],
                    [
                        'title' => 'Options in the slot',
                        'description' => 'Write your own options and optgroups.',
                        'code' => <<<'BLADE'
                            <x-gui::field label="Timezone" name="timezone">
                                <x-gui::select>
                                    <optgroup label="Europe">
                                        <option>Europe/London</option>
                                        <option>Europe/Paris</option>
                                    </optgroup>
                                    <optgroup label="Oceania">
                                        <option selected>Australia/Sydney</option>
                                        <option>Pacific/Auckland</option>
                                    </optgroup>
                                </x-gui::select>
                            </x-gui::field>
                            BLADE,
                    ],
                ],
                'props' => [
                    ['options', 'iterable', 'null', 'value => label pairs.'],
                    ['placeholder', 'string', 'null', 'A disabled first option, selected when there is no value.'],
                    ['value', 'string | array', 'null', 'The selected value. Defaults to old input.'],
                    ['size', "'sm' | 'md' | 'lg'", "'md'", 'Height and text size.'],
                    ['invalid', 'bool | null', 'null', 'Force the invalid state.'],
                ],
                'slots' => [
                    ['default', 'Extra option and optgroup elements.'],
                ],
            ],

            'combobox' => [
                'name' => 'Combobox',
                'group' => 'Forms',
                'description' => 'A select you can type into to filter. Follows the WAI-ARIA combobox pattern. Needs Alpine, which Livewire includes.',
                'preview' => 'narrow',
                'usage' => '<x-gui::combobox name="country" :options="$countries" placeholder="Search countries" />',
                'examples' => [
                    [
                        'title' => 'In a field',
                        'description' => 'Type to filter. Arrow keys move through the list, Enter picks, Escape closes the list (and clears the value when it is already closed). The value is submitted through a hidden input named after the field.',
                        'code' => <<<'BLADE'
                            <x-gui::field label="Country" name="country" hint="Where you're based.">
                                <x-gui::combobox :options="$countries" placeholder="Search countries" />
                            </x-gui::field>
                            BLADE,
                    ],
                    [
                        'title' => 'With a value',
                        'code' => <<<'BLADE'
                            <x-gui::field label="Role" name="role">
                                <x-gui::combobox :options="$roles" value="editor" />
                            </x-gui::field>
                            BLADE,
                    ],
                    [
                        'title' => 'In a modal',
                        'description' => 'The list is a top-layer popover, so it is never clipped by a modal or a scrolling container.',
                        'code' => <<<'BLADE'
                            <x-gui::button variant="outline" commandfor="move-project" command="show-modal">Move project</x-gui::button>

                            <x-gui::modal id="move-project" title="Move project" size="sm">
                                <x-gui::field label="Region" name="region">
                                    <x-gui::combobox :options="$countries" placeholder="Search regions" />
                                </x-gui::field>
                                <x-slot:footer>
                                    <x-gui::modal.close>Cancel</x-gui::modal.close>
                                    <x-gui::button commandfor="move-project" command="close">Move</x-gui::button>
                                </x-slot:footer>
                            </x-gui::modal>
                            BLADE,
                    ],
                ],
                'props' => [
                    ['options', 'array', '[]', 'value => label pairs.'],
                    ['value', 'string', 'null', 'The selected value. Defaults to old input.'],
                    ['placeholder', 'string', 'null', ''],
                    ['size', "'sm' | 'md' | 'lg'", "'md'", ''],
                    ['invalid', 'bool | null', 'null', 'Force the invalid state.'],
                    ['empty', 'string', "'No results'", 'Shown when nothing matches.'],
                ],
                'slots' => [],
                'notes' => [
                    'wire:model and x-model bind the value; other attributes go on the text input. The selected value fires a change event on the hidden input.',
                    'Options are filtered in the browser. For very large lists, search on the server and pass fewer options.',
                ],
            ],

            'checkbox' => [
                'name' => 'Checkbox',
                'group' => 'Forms',
                'description' => 'A native checkbox with a label and optional description.',
                'preview' => 'narrow',
                'usage' => '<x-gui::checkbox name="remember" label="Remember me" />',
                'examples' => [
                    [
                        'title' => 'With a description',
                        'code' => <<<'BLADE'
                            <x-gui::checkbox name="marketing" label="Product updates" description="A short email when we ship something new." checked />
                            BLADE,
                    ],
                    [
                        'title' => 'Rich label',
                        'description' => 'Use the slot when the label needs markup.',
                        'code' => <<<'BLADE'
                            <x-gui::checkbox name="terms">
                                I agree to the <a href="#" class="underline">terms of service</a>
                            </x-gui::checkbox>
                            BLADE,
                    ],
                    [
                        'title' => 'Group',
                        'description' => 'Wrap related checkboxes in a fieldset. Each gets its own id from its name and value.',
                        'code' => <<<'BLADE'
                            <x-gui::fieldset legend="Notify me about" name="notify">
                                <x-gui::checkbox name="notify[]" value="comments" label="Comments" checked />
                                <x-gui::checkbox name="notify[]" value="mentions" label="Mentions" checked />
                                <x-gui::checkbox name="notify[]" value="follows" label="New followers" />
                            </x-gui::fieldset>
                            BLADE,
                    ],
                    [
                        'title' => 'Disabled',
                        'code' => <<<'BLADE'
                            <x-gui::checkbox name="locked" label="Managed by your admin" checked disabled />
                            BLADE,
                    ],
                ],
                'props' => [
                    ['label', 'string', 'null', 'Label text. Or use the slot.'],
                    ['description', 'string', 'null', 'Help text, linked with aria-describedby.'],
                    ['invalid', 'bool | null', 'null', 'Force the invalid state. By default it follows the error bag for name.'],
                ],
                'slots' => [
                    ['default', 'The label, when it needs markup.'],
                ],
            ],

            'radio' => [
                'name' => 'Radio',
                'group' => 'Forms',
                'description' => 'A native radio button. Group them in a fieldset.',
                'preview' => 'narrow',
                'usage' => '<x-gui::radio name="plan" value="pro" label="Pro" />',
                'examples' => [
                    [
                        'title' => 'Radio group',
                        'code' => <<<'BLADE'
                            <x-gui::fieldset legend="Plan" hint="You can change this later." name="plan">
                                <x-gui::radio name="plan" value="free" label="Free" description="For side projects." />
                                <x-gui::radio name="plan" value="pro" label="Pro" description="For growing teams." checked />
                                <x-gui::radio name="plan" value="enterprise" label="Enterprise" description="Talk to us." disabled />
                            </x-gui::fieldset>
                            BLADE,
                    ],
                    [
                        'title' => 'Group error',
                        'code' => <<<'BLADE'
                            <x-gui::fieldset legend="Delivery" name="delivery" error="Choose a delivery option.">
                                <x-gui::radio name="delivery" value="standard" label="Standard" :invalid="true" />
                                <x-gui::radio name="delivery" value="express" label="Express" :invalid="true" />
                            </x-gui::fieldset>
                            BLADE,
                    ],
                ],
                'props' => [
                    ['label', 'string', 'null', 'Label text. Or use the slot.'],
                    ['description', 'string', 'null', 'Help text, linked with aria-describedby.'],
                    ['invalid', 'bool | null', 'null', 'Force the invalid state.'],
                ],
                'slots' => [
                    ['default', 'The label, when it needs markup.'],
                ],
            ],

            'switch' => [
                'name' => 'Switch',
                'group' => 'Forms',
                'description' => 'An on/off toggle. A real checkbox with role="switch" underneath.',
                'preview' => 'narrow',
                'usage' => '<x-gui::switch name="notifications" label="Email notifications" />',
                'examples' => [
                    [
                        'title' => 'With a description',
                        'code' => <<<'BLADE'
                            <x-gui::switch name="digest" label="Weekly digest" description="A summary of activity every Monday." checked />
                            BLADE,
                    ],
                    [
                        'title' => 'Settings list',
                        'code' => <<<'BLADE'
                            <x-gui::fieldset legend="Privacy">
                                <x-gui::switch name="public" label="Public profile" checked />
                                <x-gui::switch name="search" label="Show me in search results" />
                                <x-gui::switch name="analytics" label="Share usage data" disabled />
                            </x-gui::fieldset>
                            BLADE,
                    ],
                ],
                'props' => [
                    ['label', 'string', 'null', 'Label text. Or use the slot.'],
                    ['description', 'string', 'null', 'Help text, linked with aria-describedby.'],
                ],
                'slots' => [
                    ['default', 'The label, when it needs markup.'],
                ],
            ],

            'fieldset' => [
                'name' => 'Fieldset',
                'group' => 'Forms',
                'description' => 'Groups related controls under a legend, with a shared hint and error.',
                'preview' => 'narrow',
                'usage' => <<<'BLADE'
                    <x-gui::fieldset legend="Plan" name="plan">
                        …
                    </x-gui::fieldset>
                    BLADE,
                'examples' => [
                    [
                        'title' => 'Address',
                        'code' => <<<'BLADE'
                            <x-gui::fieldset legend="Shipping address" hint="We only ship within Australia.">
                                <x-gui::field label="Street" name="address[street]">
                                    <x-gui::input autocomplete="street-address" />
                                </x-gui::field>
                                <div class="grid grid-cols-2 gap-3">
                                    <x-gui::field label="City" name="address[city]">
                                        <x-gui::input autocomplete="address-level2" />
                                    </x-gui::field>
                                    <x-gui::field label="Postcode" name="address[postcode]">
                                        <x-gui::input autocomplete="postal-code" inputmode="numeric" />
                                    </x-gui::field>
                                </div>
                            </x-gui::fieldset>
                            BLADE,
                    ],
                ],
                'props' => [
                    ['legend', 'string', 'null', 'The group\'s label.'],
                    ['hint', 'string', 'null', 'Help text for the whole group.'],
                    ['name', 'string', 'null', 'Used to find a group error, e.g. "roles" for roles[] checkboxes.'],
                    ['error', 'string', 'null', 'An error message. Defaults to the first error for name.'],
                ],
                'slots' => [
                    ['default', 'The grouped controls.'],
                ],
            ],

            'label' => [
                'name' => 'Label',
                'group' => 'Forms',
                'description' => 'A form label. Field uses it for you; use it directly for custom layouts.',
                'preview' => 'narrow',
                'usage' => '<x-gui::label for="email">Email</x-gui::label>',
                'examples' => [
                    [
                        'title' => 'Required',
                        'description' => 'The asterisk is hidden from screen readers; put required on the control so they announce it.',
                        'code' => <<<'BLADE'
                            <x-gui::label for="company" required>Company</x-gui::label>
                            <x-gui::input id="company" name="company" required />
                            BLADE,
                    ],
                ],
                'props' => [
                    ['required', 'bool', 'false', 'Shows a required marker.'],
                ],
                'slots' => [
                    ['default', 'The label text.'],
                ],
            ],

            'alert' => [
                'name' => 'Alert',
                'group' => 'Display',
                'description' => 'A message that stands out from the page, with an icon, title and actions.',
                'preview' => 'block',
                'usage' => '<x-gui::alert color="success" title="Saved">Your changes are live.</x-gui::alert>',
                'playground' => [
                    'template' => '<x-gui::alert :color="$color" :title="$title ?: null" :icon="! $hideIcon" class="w-full max-w-md">{{ $label }}</x-gui::alert>',
                    'controls' => [
                        ['name' => 'color', 'type' => 'select', 'options' => ['info', 'success', 'warning', 'danger', 'primary', 'secondary'], 'default' => 'info'],
                        ['name' => 'title', 'type' => 'text', 'default' => 'Heads up', 'always' => true],
                        ['name' => 'label', 'type' => 'text', 'default' => 'Something you should know about.', 'slot' => true],
                        ['name' => 'hideIcon', 'label' => 'hide icon', 'type' => 'boolean', 'default' => false, 'attribute' => ':icon="false"'],
                    ],
                ],
                'examples' => [
                    [
                        'title' => 'Colours',
                        'code' => <<<'BLADE'
                            <div class="space-y-3">
                                <x-gui::alert title="New version available">Refresh to get the latest features.</x-gui::alert>
                                <x-gui::alert color="success" title="Payment received">Thanks! A receipt is on its way.</x-gui::alert>
                                <x-gui::alert color="warning" title="Your trial ends in 3 days">Add a card to keep your projects.</x-gui::alert>
                                <x-gui::alert color="danger" title="Deploy failed">The build exited with code 1.</x-gui::alert>
                            </div>
                            BLADE,
                    ],
                    [
                        'title' => 'With actions',
                        'code' => <<<'BLADE'
                            <x-gui::alert color="warning" title="Unsaved changes">
                                You have edits that haven't been published.
                                <x-slot:actions>
                                    <x-gui::button size="sm" variant="ghost" color="warning">Discard</x-gui::button>
                                    <x-gui::button size="sm" color="warning">Publish</x-gui::button>
                                </x-slot:actions>
                            </x-gui::alert>
                            BLADE,
                    ],
                    [
                        'title' => 'Announced to screen readers',
                        'description' => 'Alerts are static by default. When one appears after the page loads, add role="alert" (urgent) or role="status" (polite).',
                        'code' => <<<'BLADE'
                            <x-gui::alert color="success" role="status">Settings saved.</x-gui::alert>
                            BLADE,
                    ],
                ],
                'props' => [
                    ['color', "'info' | 'success' | 'warning' | 'danger' | 'primary' | 'secondary'", "'info'", 'Colour and default icon.'],
                    ['title', 'string', 'null', 'A bold first line.'],
                    ['icon', 'bool', 'true', 'Set :icon="false" to hide the icon, or use the icon slot to replace it.'],
                ],
                'slots' => [
                    ['default', 'The message.'],
                    ['icon', 'Replaces the default icon.'],
                    ['actions', 'Buttons or links on the right.'],
                ],
            ],

            'avatar' => [
                'name' => 'Avatar',
                'group' => 'Display',
                'description' => 'A profile image, with initials when there is no image.',
                'usage' => '<x-gui::avatar :src="$user->photo_url" :name="$user->name" />',
                'playground' => [
                    'selfClosing' => true,
                    'template' => '<x-gui::avatar :name="$name ?: null" :size="$size" />',
                    'controls' => [
                        ['name' => 'name', 'type' => 'text', 'default' => 'Ada Lovelace', 'always' => true],
                        ['name' => 'size', 'type' => 'select', 'options' => ['sm', 'md', 'lg'], 'default' => 'md'],
                    ],
                ],
                'examples' => [
                    [
                        'title' => 'Image',
                        'code' => <<<'BLADE'
                            <x-gui::avatar :src="$photo" name="Ada Lovelace" size="lg" />
                            BLADE,
                    ],
                    [
                        'title' => 'Initials and fallback',
                        'code' => <<<'BLADE'
                            <x-gui::avatar name="Grace Hopper" size="sm" />
                            <x-gui::avatar name="Alan Turing" />
                            <x-gui::avatar name="Émile Ångström" size="lg" />
                            <x-gui::avatar />
                            BLADE,
                    ],
                    [
                        'title' => 'Stacked',
                        'code' => <<<'BLADE'
                            <div class="flex -space-x-2">
                                @foreach ($users as $user)
                                    <x-gui::avatar :name="$user['name']" class="ring-2 ring-white dark:ring-zinc-950" />
                                @endforeach
                            </div>
                            BLADE,
                    ],
                ],
                'props' => [
                    ['src', 'string', 'null', 'Image URL.'],
                    ['name', 'string', 'null', 'Used for initials and as the accessible name.'],
                    ['alt', 'string', 'null', 'Overrides the accessible name.'],
                    ['size', "'sm' | 'md' | 'lg'", "'md'", ''],
                ],
                'slots' => [],
            ],

            'badge' => [
                'name' => 'Badge',
                'group' => 'Display',
                'description' => 'A small label for status, counts or categories.',
                'usage' => '<x-gui::badge color="success">Active</x-gui::badge>',
                'playground' => [
                    'template' => '<x-gui::badge :variant="$variant" :color="$color" :size="$size">{{ $label }}</x-gui::badge>',
                    'controls' => [
                        ['name' => 'variant', 'type' => 'select', 'options' => ['soft', 'solid', 'outline'], 'default' => 'soft'],
                        ['name' => 'color', 'type' => 'select', 'options' => ['primary', 'secondary', 'success', 'warning', 'danger', 'info'], 'default' => 'secondary'],
                        ['name' => 'size', 'type' => 'select', 'options' => ['sm', 'md'], 'default' => 'md'],
                        ['name' => 'label', 'type' => 'text', 'default' => 'Badge', 'slot' => true],
                    ],
                ],
                'examples' => [
                    [
                        'title' => 'Colours',
                        'code' => <<<'BLADE'
                            <x-gui::badge color="primary">Primary</x-gui::badge>
                            <x-gui::badge>Secondary</x-gui::badge>
                            <x-gui::badge color="success">Success</x-gui::badge>
                            <x-gui::badge color="warning">Warning</x-gui::badge>
                            <x-gui::badge color="danger">Danger</x-gui::badge>
                            <x-gui::badge color="info">Info</x-gui::badge>
                            BLADE,
                    ],
                    [
                        'title' => 'Variants',
                        'code' => <<<'BLADE'
                            <x-gui::badge color="primary">Soft</x-gui::badge>
                            <x-gui::badge color="primary" variant="solid">Solid</x-gui::badge>
                            <x-gui::badge color="primary" variant="outline">Outline</x-gui::badge>
                            BLADE,
                    ],
                    [
                        'title' => 'With a dot',
                        'code' => <<<'BLADE'
                            <x-gui::badge color="success">
                                <svg class="size-1.5 fill-current" viewBox="0 0 6 6" aria-hidden="true"><circle cx="3" cy="3" r="3" /></svg>
                                Online
                            </x-gui::badge>
                            BLADE,
                    ],
                ],
                'props' => [
                    ['variant', "'soft' | 'solid' | 'outline'", "'soft'", ''],
                    ['color', "'primary' | 'secondary' | 'success' | 'warning' | 'danger' | 'info'", "'secondary'", ''],
                    ['size', "'sm' | 'md'", "'md'", ''],
                ],
                'slots' => [
                    ['default', 'The label.'],
                ],
            ],

            'spinner' => [
                'name' => 'Spinner',
                'group' => 'Display',
                'description' => 'Shows that something is loading. Buttons use it for their loading state.',
                'usage' => '<x-gui::spinner label="Loading results" />',
                'examples' => [
                    [
                        'title' => 'Sizes',
                        'code' => <<<'BLADE'
                            <x-gui::spinner size="sm" />
                            <x-gui::spinner />
                            <x-gui::spinner size="lg" class="text-gui-primary-text" />
                            BLADE,
                    ],
                    [
                        'title' => 'Announced',
                        'description' => 'Give it a label when it stands alone, so screen readers hear it. Without one it\'s decorative.',
                        'code' => <<<'BLADE'
                            <div class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                <x-gui::spinner size="sm" label="Loading results" />
                                Loading results…
                            </div>
                            BLADE,
                    ],
                ],
                'props' => [
                    ['size', "'sm' | 'md' | 'lg'", "'md'", ''],
                    ['label', 'string', 'null', 'Announced as a status. Without it the spinner is aria-hidden.'],
                ],
                'slots' => [],
                'notes' => [
                    'It inherits the text colour. With reduced motion it spins more slowly rather than stopping, since it is the only sign of progress.',
                ],
            ],

            'icon' => [
                'name' => 'Icon',
                'group' => 'Display',
                'description' => 'Heroicons that GiraffeUI ships with, or any icon from a blade-icons set.',
                'usage' => '<x-gui::icon name="check" />',
                'examples' => [
                    [
                        'title' => 'Bundled icons',
                        'description' => 'These come with GiraffeUI and are what its components use.',
                        'code' => <<<'BLADE'
                            <div class="grid grid-cols-3 gap-3 sm:grid-cols-6">
                                @foreach (array_keys(\JayAitch\GiraffeUI\Support\Icons::PATHS) as $name)
                                    <div class="flex flex-col items-center gap-2 rounded-gui p-3 text-center text-xs text-zinc-600 dark:text-zinc-400">
                                        <x-gui::icon :name="$name" class="text-zinc-900 dark:text-zinc-100" />
                                        {{ $name }}
                                    </div>
                                @endforeach
                            </div>
                            BLADE,
                    ],
                    [
                        'title' => 'Sizes and colours',
                        'code' => <<<'BLADE'
                            <x-gui::icon name="check-circle" size="xs" class="text-gui-success-text" />
                            <x-gui::icon name="check-circle" size="sm" class="text-gui-success-text" />
                            <x-gui::icon name="check-circle" class="text-gui-success-text" />
                            <x-gui::icon name="check-circle" size="lg" class="text-gui-success-text" />
                            BLADE,
                    ],
                    [
                        'title' => 'Meaningful icons',
                        'description' => 'Icons are hidden from screen readers unless you give them a label. Label an icon when it carries meaning on its own.',
                        'code' => <<<'BLADE'
                            <p class="flex items-center gap-2 text-sm">
                                <x-gui::icon name="exclamation-triangle" size="sm" label="Warning" class="text-gui-warning-text" />
                                Your card expires this month.
                            </p>
                            BLADE,
                    ],
                ],
                'props' => [
                    ['name', 'string', 'required', 'A bundled icon, or any blade-icons name such as heroicon-o-bell.'],
                    ['size', "'xs' | 'sm' | 'md' | 'lg'", "'md'", '14, 16, 20 or 24px.'],
                    ['label', 'string', 'null', 'Announces the icon as an image. Without it the icon is aria-hidden.'],
                ],
                'slots' => [],
                'notes' => [
                    'For any other icon, install blade-ui-kit/blade-icons and an icon set (e.g. blade-ui-kit/blade-heroicons), then use its names: <x-gui::icon name="heroicon-o-bell" />.',
                ],
            ],

            'progress' => [
                'name' => 'Progress',
                'group' => 'Display',
                'description' => 'Shows how far along a task is, or that it is working when you can\'t tell.',
                'preview' => 'narrow',
                'usage' => '<x-gui::progress :value="$percent" label="Uploading" />',
                'examples' => [
                    [
                        'title' => 'Determinate',
                        'code' => <<<'BLADE'
                            <x-gui::progress :value="64" label="Uploading photos" show-value />
                            <x-gui::progress :value="3" :max="4" label="Step 3 of 4" color="success" />
                            BLADE,
                    ],
                    [
                        'title' => 'Indeterminate',
                        'description' => 'Leave out value when you can\'t measure progress.',
                        'code' => <<<'BLADE'
                            <x-gui::progress label="Preparing your export" />
                            BLADE,
                    ],
                ],
                'props' => [
                    ['value', 'number', 'null', 'Progress so far. Leave it out for indeterminate.'],
                    ['max', 'number', '100', ''],
                    ['label', 'string', 'null', 'Shown above the bar and used as its accessible name.'],
                    ['color', "'primary' | 'secondary' | 'success' | 'warning' | 'danger' | 'info'", "'primary'", ''],
                    ['show-value', 'bool', 'false', 'Show the percentage.'],
                ],
                'slots' => [],
                'notes' => [
                    'Always give it a label, or an aria-label, so screen readers know what is progressing.',
                ],
            ],

            'kbd' => [
                'name' => 'Kbd',
                'group' => 'Display',
                'description' => 'A keyboard key, for shortcuts and instructions.',
                'usage' => '<x-gui::kbd>⌘</x-gui::kbd> <x-gui::kbd>K</x-gui::kbd>',
                'examples' => [
                    [
                        'title' => 'Shortcuts',
                        'code' => <<<'BLADE'
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                Press <x-gui::kbd>⌘</x-gui::kbd> <x-gui::kbd>K</x-gui::kbd> to search, or <x-gui::kbd>Esc</x-gui::kbd> to close.
                            </p>
                            BLADE,
                    ],
                ],
                'props' => [],
                'slots' => [
                    ['default', 'The key.'],
                ],
            ],

            'skeleton' => [
                'name' => 'Skeleton',
                'group' => 'Display',
                'description' => 'A placeholder shaped like the content that is loading.',
                'preview' => 'block',
                'usage' => '<x-gui::skeleton class="h-4 w-48" />',
                'examples' => [
                    [
                        'title' => 'Card placeholder',
                        'description' => 'Skeletons are hidden from screen readers. Mark the loading region with aria-busy="true" instead.',
                        'code' => <<<'BLADE'
                            <div aria-busy="true" class="mx-auto flex max-w-sm items-start gap-4">
                                <x-gui::skeleton class="size-10 shrink-0 rounded-full!" />
                                <div class="flex-1 space-y-3">
                                    <x-gui::skeleton class="h-4 w-1/2" />
                                    <x-gui::skeleton :lines="3" />
                                </div>
                            </div>
                            BLADE,
                    ],
                    [
                        'title' => 'With Livewire',
                        'description' => 'Show skeletons while a lazy component loads, or with wire:loading.',
                        'code' => <<<'BLADE'
                            <div class="mx-auto max-w-sm space-y-2">
                                <x-gui::skeleton class="h-8 w-full" />
                                <x-gui::skeleton class="h-8 w-full" />
                                <x-gui::skeleton class="h-8 w-full" />
                            </div>
                            BLADE,
                    ],
                ],
                'props' => [
                    ['lines', 'int', 'null', 'Render this many text lines, the last one shorter.'],
                ],
                'slots' => [],
                'notes' => [
                    'Size and shape it with classes. With reduced motion it stops pulsing.',
                    'In a Livewire component, return skeletons from placeholder() for lazy loading.',
                ],
            ],

            'empty-state' => [
                'name' => 'Empty state',
                'group' => 'Display',
                'description' => 'What to show when there is nothing to show yet, with a way forward.',
                'preview' => 'block',
                'usage' => '<x-gui::empty-state title="No projects yet" description="Create one to get started." />',
                'examples' => [
                    [
                        'title' => 'With an icon and actions',
                        'code' => <<<'BLADE'
                            <x-gui::empty-state title="No projects yet" description="Projects group your deployments, domains and team. Create one to get started.">
                                <x-slot:icon>
                                    <svg class="size-6" viewBox="0 0 20 20" fill="currentColor"><path d="M3.75 3A1.75 1.75 0 0 0 2 4.75v3.26a3.235 3.235 0 0 1 1.75-.51h12.5c.644 0 1.245.188 1.75.51V6.75A1.75 1.75 0 0 0 16.25 5h-4.836a.25.25 0 0 1-.177-.073L9.823 3.513A1.75 1.75 0 0 0 8.586 3H3.75ZM3.75 9A1.75 1.75 0 0 0 2 10.75v4.5c0 .966.784 1.75 1.75 1.75h12.5A1.75 1.75 0 0 0 18 15.25v-4.5A1.75 1.75 0 0 0 16.25 9H3.75Z" /></svg>
                                </x-slot:icon>
                                <x-slot:actions>
                                    <x-gui::button>New project</x-gui::button>
                                    <x-gui::button variant="ghost" color="secondary" href="#">Import</x-gui::button>
                                </x-slot:actions>
                            </x-gui::empty-state>
                            BLADE,
                    ],
                    [
                        'title' => 'In a table',
                        'code' => <<<'BLADE'
                            <x-gui::table :columns="['name', 'email']" :rows="[]">
                                <x-slot:empty>
                                    <x-gui::empty-state title="No members" description="Invite people to collaborate on this project." class="py-6!">
                                        <x-slot:actions>
                                            <x-gui::button size="sm" variant="outline">Invite</x-gui::button>
                                        </x-slot:actions>
                                    </x-gui::empty-state>
                                </x-slot:empty>
                            </x-gui::table>
                            BLADE,
                    ],
                ],
                'props' => [
                    ['title', 'string', 'required', 'A short heading (an h3).'],
                    ['description', 'string', 'null', 'What this is and what to do next.'],
                ],
                'slots' => [
                    ['icon', 'A decorative icon above the title.'],
                    ['actions', 'Buttons or links.'],
                ],
            ],

            'card' => [
                'name' => 'Card',
                'group' => 'Display',
                'description' => 'A surface for grouping content, with an optional header and footer.',
                'preview' => 'block',
                'usage' => <<<'BLADE'
                    <x-gui::card title="Team" description="Manage who has access.">
                        …
                    </x-gui::card>
                    BLADE,
                'examples' => [
                    [
                        'title' => 'Header, body and footer',
                        'code' => <<<'BLADE'
                            <x-gui::card title="Create project" description="Deploy a new project in one click." class="mx-auto max-w-md">
                                <div class="space-y-4">
                                    <x-gui::field label="Name" name="project">
                                        <x-gui::input placeholder="my-app" />
                                    </x-gui::field>
                                    <x-gui::field label="Framework" name="framework">
                                        <x-gui::select :options="['laravel' => 'Laravel', 'statamic' => 'Statamic']" />
                                    </x-gui::field>
                                </div>

                                <x-slot:footer>
                                    <x-gui::button variant="ghost" color="secondary">Cancel</x-gui::button>
                                    <x-gui::button class="ml-auto">Deploy</x-gui::button>
                                </x-slot:footer>
                            </x-gui::card>
                            BLADE,
                    ],
                    [
                        'title' => 'With header actions',
                        'code' => <<<'BLADE'
                            <x-gui::card title="Members" description="4 people" class="mx-auto max-w-md">
                                <x-slot:actions>
                                    <x-gui::button size="sm" variant="outline" color="secondary">Invite</x-gui::button>
                                </x-slot:actions>

                                <ul class="space-y-3">
                                    @foreach ($users as $user)
                                        <li class="flex items-center gap-3">
                                            <x-gui::avatar :name="$user['name']" size="sm" />
                                            <span class="text-sm font-medium">{{ $user['name'] }}</span>
                                            <x-gui::badge class="ml-auto" size="sm">{{ $user['role'] }}</x-gui::badge>
                                        </li>
                                    @endforeach
                                </ul>
                            </x-gui::card>
                            BLADE,
                    ],
                ],
                'props' => [
                    ['title', 'string', 'null', 'Heading (an h3).'],
                    ['description', 'string', 'null', 'Text under the title.'],
                ],
                'slots' => [
                    ['default', 'The body.'],
                    ['actions', 'Buttons in the header, on the right.'],
                    ['footer', 'A footer row with a top border.'],
                ],
            ],

            'separator' => [
                'name' => 'Separator',
                'group' => 'Display',
                'description' => 'A line that divides content, optionally with a label.',
                'preview' => 'block',
                'usage' => '<x-gui::separator />',
                'examples' => [
                    [
                        'title' => 'With a label',
                        'code' => <<<'BLADE'
                            <div class="mx-auto max-w-sm space-y-4">
                                <x-gui::button variant="outline" color="secondary" class="w-full">Continue with GitHub</x-gui::button>
                                <x-gui::separator label="or" />
                                <x-gui::input type="email" placeholder="you@example.com" aria-label="Email" />
                            </div>
                            BLADE,
                    ],
                    [
                        'title' => 'Vertical',
                        'code' => <<<'BLADE'
                            <div class="flex h-6 items-center gap-4 text-sm">
                                <a href="#">Docs</a>
                                <x-gui::separator orientation="vertical" />
                                <a href="#">Blog</a>
                                <x-gui::separator orientation="vertical" />
                                <a href="#">Changelog</a>
                            </div>
                            BLADE,
                    ],
                ],
                'props' => [
                    ['orientation', "'horizontal' | 'vertical'", "'horizontal'", ''],
                    ['label', 'string', 'null', 'Text in the middle of a horizontal line.'],
                ],
                'slots' => [],
            ],

            'breadcrumbs' => [
                'name' => 'Breadcrumbs',
                'group' => 'Navigation',
                'description' => 'Shows where the current page sits in the site. Follows the WAI-ARIA breadcrumb pattern.',
                'usage' => '<x-gui::breadcrumbs :items="[\'Home\' => \'/\', \'Projects\' => \'/projects\', \'GiraffeUI\' => null]" />',
                'examples' => [
                    [
                        'title' => 'From an array',
                        'description' => 'Pass label => url pairs. The item with a null url is the current page and gets aria-current="page".',
                        'code' => <<<'BLADE'
                            <x-gui::breadcrumbs :items="['Home' => '#', 'Projects' => '#', 'GiraffeUI' => null]" />
                            BLADE,
                    ],
                    [
                        'title' => 'Composed, with an icon',
                        'code' => <<<'BLADE'
                            <x-gui::breadcrumbs>
                                <x-gui::breadcrumbs.item href="#">
                                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M9.293 2.293a1 1 0 0 1 1.414 0l7 7A1 1 0 0 1 17 11h-1v6a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-3a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6H3a1 1 0 0 1-.707-1.707l7-7Z" clip-rule="evenodd" /></svg>
                                    <span class="sr-only">Home</span>
                                </x-gui::breadcrumbs.item>
                                <x-gui::breadcrumbs.item href="#">Settings</x-gui::breadcrumbs.item>
                                <x-gui::breadcrumbs.item>Billing</x-gui::breadcrumbs.item>
                            </x-gui::breadcrumbs>
                            BLADE,
                    ],
                ],
                'props' => [
                    ['items', 'array', 'null', 'label => url pairs. Use null for the current page. Or compose breadcrumbs.item.'],
                    ['href (item)', 'string', 'null', 'Leave it out on the current page.'],
                ],
                'slots' => [
                    ['default', 'breadcrumbs.item elements, when not using items.'],
                ],
                'notes' => [
                    'Separators are decorative and hidden from screen readers; the list order already conveys the hierarchy.',
                ],
            ],

            'modal' => [
                'name' => 'Modal',
                'group' => 'Overlays',
                'description' => 'A dialog over the page. Built on the native <dialog> element, so focus trapping, Escape and the backdrop work without JavaScript.',
                'preview' => 'inline',
                'usage' => <<<'BLADE'
                    <x-gui::button commandfor="confirm" command="show-modal">Open</x-gui::button>

                    <x-gui::modal id="confirm" title="Are you sure?">
                        …
                    </x-gui::modal>
                    BLADE,
                'examples' => [
                    [
                        'title' => 'Confirm',
                        'description' => 'Buttons open and close the dialog with the command and commandfor attributes. No JavaScript needed.',
                        'code' => <<<'BLADE'
                            <x-gui::button color="danger" commandfor="delete-project" command="show-modal">Delete project</x-gui::button>

                            <x-gui::modal id="delete-project" title="Delete project?" description="This permanently deletes the project and its deployments." size="sm">
                                <x-slot:footer>
                                    <x-gui::modal.close>Cancel</x-gui::modal.close>
                                    <x-gui::button color="danger">Delete</x-gui::button>
                                </x-slot:footer>
                            </x-gui::modal>
                            BLADE,
                    ],
                    [
                        'title' => 'With a form',
                        'description' => 'Put the form around the body and footer. After a failed submission, reopen it with :open="$errors->any()".',
                        'code' => <<<'BLADE'
                            <x-gui::button variant="outline" commandfor="edit-profile" command="show-modal">Edit profile</x-gui::button>

                            <x-gui::modal id="edit-profile" title="Edit profile" description="Changes are visible to your team.">
                                <form id="edit-profile-form" class="space-y-4" onsubmit="event.preventDefault(); this.closest('dialog').close()">
                                    <x-gui::field label="Name" name="name">
                                        <x-gui::input value="Ada Lovelace" />
                                    </x-gui::field>
                                    <x-gui::field label="Bio" name="bio">
                                        <x-gui::textarea>Analyst, metaphysician and founder of scientific computing.</x-gui::textarea>
                                    </x-gui::field>
                                </form>

                                <x-slot:footer>
                                    <x-gui::modal.close>Cancel</x-gui::modal.close>
                                    <x-gui::button type="submit" form="edit-profile-form">Save</x-gui::button>
                                </x-slot:footer>
                            </x-gui::modal>
                            BLADE,
                    ],
                    [
                        'title' => 'Required action',
                        'description' => 'With :dismissible="false" there is no close button, and Escape and backdrop clicks are ignored where the browser supports closedby.',
                        'code' => <<<'BLADE'
                            <x-gui::button variant="outline" color="secondary" commandfor="terms" command="show-modal">Show terms</x-gui::button>

                            <x-gui::modal id="terms" title="Updated terms" :dismissible="false" size="sm">
                                We've updated our terms of service. Please accept them to continue.

                                <x-slot:footer>
                                    <x-gui::button commandfor="terms" command="close">Accept</x-gui::button>
                                </x-slot:footer>
                            </x-gui::modal>
                            BLADE,
                    ],
                    [
                        'title' => 'Long content',
                        'description' => 'The body scrolls; the header and footer stay put.',
                        'code' => <<<'BLADE'
                            <x-gui::button variant="ghost" commandfor="changelog" command="show-modal">What's new</x-gui::button>

                            <x-gui::modal id="changelog" title="What's new" size="lg">
                                <div class="space-y-4">
                                    @foreach (range(1, 12) as $release)
                                        <div>
                                            <p class="font-medium">Release 1.{{ $release }}</p>
                                            <p class="text-zinc-600 dark:text-zinc-400">Faster deploys, fewer bugs and a better dark mode.</p>
                                        </div>
                                    @endforeach
                                </div>

                                <x-slot:footer>
                                    <x-gui::modal.close>Close</x-gui::modal.close>
                                </x-slot:footer>
                            </x-gui::modal>
                            BLADE,
                    ],
                ],
                'props' => [
                    ['id', 'string', 'required', 'Used by buttons to open and close it.'],
                    ['title', 'string', 'null', 'Heading, linked with aria-labelledby.'],
                    ['description', 'string', 'null', 'Text under the title, linked with aria-describedby.'],
                    ['size', "'sm' | 'md' | 'lg' | 'xl'", "'md'", 'Maximum width.'],
                    ['dismissible', 'bool', 'true', 'Show a close button and allow Escape and backdrop clicks.'],
                    ['open', 'bool', 'false', 'Open when the page loads, e.g. :open="$errors->any()".'],
                ],
                'slots' => [
                    ['default', 'The body. Scrolls when it is tall.'],
                    ['footer', 'Actions. Stacked on small screens, right-aligned on larger ones.'],
                ],
                'notes' => [
                    'Open with <button commandfor="id" command="show-modal">, close with command="close" or <x-gui::modal.close>.',
                    'From JavaScript: document.getElementById(\'id\').showModal() and .close().',
                    'From Livewire: $this->js("document.getElementById(\'id\').showModal()"). The dialog has wire:ignore.self, so re-renders don\'t close it.',
                ],
            ],

            'drawer' => [
                'name' => 'Drawer',
                'group' => 'Overlays',
                'description' => 'A panel that slides in from the side. The same native <dialog> as Modal, so focus trapping, Escape and the backdrop work without JavaScript.',
                'usage' => <<<'BLADE'
                    <x-gui::button commandfor="cart" command="show-modal">Cart</x-gui::button>

                    <x-gui::drawer id="cart" title="Your cart">
                        …
                    </x-gui::drawer>
                    BLADE,
                'examples' => [
                    [
                        'title' => 'Cart',
                        'code' => <<<'BLADE'
                            <x-gui::button variant="outline" commandfor="cart" command="show-modal">Open cart</x-gui::button>

                            <x-gui::drawer id="cart" title="Your cart" description="2 items">
                                <ul class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                    <li class="flex justify-between py-3"><span>GiraffeUI hoodie</span><span class="font-medium">$59</span></li>
                                    <li class="flex justify-between py-3"><span>Sticker pack</span><span class="font-medium">$6</span></li>
                                </ul>

                                <x-slot:footer>
                                    <x-gui::modal.close>Keep shopping</x-gui::modal.close>
                                    <x-gui::button>Checkout · $65</x-gui::button>
                                </x-slot:footer>
                            </x-gui::drawer>
                            BLADE,
                    ],
                    [
                        'title' => 'Navigation from the left',
                        'code' => <<<'BLADE'
                            <x-gui::button variant="ghost" color="secondary" commandfor="mobile-nav" command="show-modal">
                                <x-slot:leading><x-gui::icon name="chevron-right" size="sm" /></x-slot:leading>
                                Menu
                            </x-gui::button>

                            <x-gui::drawer id="mobile-nav" side="left" size="sm" title="GiraffeUI">
                                <nav aria-label="Main" class="-mx-2 flex flex-col">
                                    <a href="#" class="rounded-gui px-2 py-2 hover:bg-zinc-100 dark:hover:bg-zinc-800">Dashboard</a>
                                    <a href="#" class="rounded-gui px-2 py-2 hover:bg-zinc-100 dark:hover:bg-zinc-800">Projects</a>
                                    <a href="#" class="rounded-gui px-2 py-2 hover:bg-zinc-100 dark:hover:bg-zinc-800">Settings</a>
                                </nav>
                            </x-gui::drawer>
                            BLADE,
                    ],
                ],
                'props' => [
                    ['id', 'string', 'required', 'Used by buttons to open and close it.'],
                    ['side', "'right' | 'left'", "'right'", 'The edge it slides in from.'],
                    ['title', 'string', 'null', 'Heading, linked with aria-labelledby.'],
                    ['description', 'string', 'null', 'Linked with aria-describedby.'],
                    ['size', "'sm' | 'md' | 'lg' | 'xl'", "'md'", 'Maximum width.'],
                    ['dismissible', 'bool', 'true', 'Close button, Escape and backdrop clicks.'],
                    ['open', 'bool', 'false', 'Open when the page loads.'],
                ],
                'slots' => [
                    ['default', 'The body. Scrolls when it is tall.'],
                    ['footer', 'Actions, pinned to the bottom.'],
                ],
                'notes' => [
                    'Open with <button commandfor="id" command="show-modal">. <x-gui::modal.close> works inside drawers too.',
                ],
            ],

            'dropdown' => [
                'name' => 'Dropdown',
                'group' => 'Overlays',
                'description' => 'A menu of actions. A native popover positioned with CSS anchor positioning, with the menu keyboard pattern added by Alpine.',
                'usage' => <<<'BLADE'
                    <x-gui::dropdown id="project-actions" label="Options">
                        <x-gui::dropdown.item wire:click="edit">Edit</x-gui::dropdown.item>
                        <x-gui::dropdown.item color="danger" wire:click="delete">Delete</x-gui::dropdown.item>
                    </x-gui::dropdown>
                    BLADE,
                'examples' => [
                    [
                        'title' => 'Actions',
                        'description' => 'Opening focuses the first item. Arrow keys, Home and End move between items; Escape, Tab or clicking outside closes it and returns focus.',
                        'code' => <<<'BLADE'
                            <x-gui::dropdown id="project-actions" label="Options">
                                <x-gui::dropdown.item x-on:click="GiraffeUI.toast('Editing…')">Edit</x-gui::dropdown.item>
                                <x-gui::dropdown.item x-on:click="GiraffeUI.toast('Duplicated', { color: 'success' })">Duplicate</x-gui::dropdown.item>
                                <x-gui::dropdown.item href="#">Settings</x-gui::dropdown.item>
                                <x-gui::dropdown.item disabled>Archive</x-gui::dropdown.item>
                                <x-gui::dropdown.separator />
                                <x-gui::dropdown.item color="danger" commandfor="confirm-delete" command="show-modal">Delete…</x-gui::dropdown.item>
                            </x-gui::dropdown>

                            <x-gui::modal id="confirm-delete" title="Delete project?" size="sm">
                                <x-slot:footer>
                                    <x-gui::modal.close>Cancel</x-gui::modal.close>
                                    <x-gui::button color="danger" commandfor="confirm-delete" command="close">Delete</x-gui::button>
                                </x-slot:footer>
                            </x-gui::modal>
                            BLADE,
                    ],
                    [
                        'title' => 'Custom trigger and position',
                        'code' => <<<'BLADE'
                            <x-gui::dropdown id="account-menu" position="bottom-end">
                                <x-slot:trigger>
                                    <button type="button" commandfor="account-menu" command="toggle-popover" aria-haspopup="menu" aria-label="Account" class="cursor-pointer rounded-full focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text">
                                        <x-gui::avatar name="Ada Lovelace" />
                                    </button>
                                </x-slot:trigger>

                                <x-gui::dropdown.item href="#">Profile</x-gui::dropdown.item>
                                <x-gui::dropdown.item href="#">Billing</x-gui::dropdown.item>
                                <x-gui::dropdown.separator />
                                <x-gui::dropdown.item>Sign out</x-gui::dropdown.item>
                            </x-gui::dropdown>
                            BLADE,
                    ],
                ],
                'props' => [
                    ['id', 'string', 'required', 'Used by the trigger to open it.'],
                    ['label', 'string', 'null', 'Text for the default trigger button.'],
                    ['position', "'bottom-start' | 'bottom' | 'bottom-end' | 'top-start' | 'top' | 'top-end' | 'left' | 'right'", "'bottom-start'", 'Flips automatically when there is no room.'],
                    ['href (item)', 'string', 'null', 'Renders the item as a link.'],
                    ['color (item)', "'danger' | null", 'null', 'For destructive actions.'],
                    ['disabled (item)', 'bool', 'false', 'Shown but not actionable.'],
                ],
                'slots' => [
                    ['trigger', 'A custom trigger. Give it commandfor="{id}" command="toggle-popover".'],
                    ['default', 'Items and separators.'],
                    ['leading (item)', 'An icon before the item label.'],
                ],
            ],

            'popover' => [
                'name' => 'Popover',
                'group' => 'Overlays',
                'description' => 'Rich content in a floating panel. A native popover, so Escape and clicking outside close it without JavaScript.',
                'usage' => <<<'BLADE'
                    <x-gui::popover id="share" label="Share">
                        …
                    </x-gui::popover>
                    BLADE,
                'examples' => [
                    [
                        'title' => 'Share',
                        'code' => <<<'BLADE'
                            <x-gui::popover id="share" label="Share">
                                <div class="w-72 space-y-3">
                                    <p class="font-medium">Share this project</p>
                                    <x-gui::input value="https://giraffeui.dev/p/8f3k" readonly aria-label="Link" />
                                    <x-gui::button size="sm" class="w-full" x-on:click="GiraffeUI.toast('Link copied', { color: 'success' })">Copy link</x-gui::button>
                                </div>
                            </x-gui::popover>
                            BLADE,
                    ],
                    [
                        'title' => 'Positions',
                        'code' => <<<'BLADE'
                            <x-gui::popover id="pop-top" label="Top" position="top">Above the trigger.</x-gui::popover>
                            <x-gui::popover id="pop-right" label="Right" position="right">Beside the trigger.</x-gui::popover>
                            <x-gui::popover id="pop-bottom-end" label="Bottom end" position="bottom-end">Below, aligned to the end.</x-gui::popover>
                            BLADE,
                    ],
                ],
                'props' => [
                    ['id', 'string', 'required', 'Used by the trigger to open it.'],
                    ['label', 'string', 'null', 'Text for the default trigger button.'],
                    ['position', "'bottom-start' | 'bottom' | 'bottom-end' | 'top-start' | 'top' | 'top-end' | 'left' | 'right'", "'bottom-start'", 'Flips automatically when there is no room.'],
                ],
                'slots' => [
                    ['trigger', 'A custom trigger. Give it commandfor="{id}" command="toggle-popover".'],
                    ['default', 'The panel content.'],
                ],
            ],

            'tooltip' => [
                'name' => 'Tooltip',
                'group' => 'Overlays',
                'description' => 'A short label shown on hover and keyboard focus. Escape dismisses it, and you can hover over it.',
                'usage' => <<<'BLADE'
                    <x-gui::tooltip text="Copy to clipboard">
                        <x-gui::button aria-label="Copy">…</x-gui::button>
                    </x-gui::tooltip>
                    BLADE,
                'examples' => [
                    [
                        'title' => 'Icon buttons',
                        'description' => 'Tooltips describe; they don\'t name. Icon-only buttons still need an aria-label.',
                        'code' => <<<'BLADE'
                            <x-gui::tooltip text="Copy to clipboard">
                                <x-gui::button variant="outline" color="secondary" size="sm" aria-label="Copy" class="w-8 px-0!">
                                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M7 3.5A1.5 1.5 0 0 1 8.5 2h3.879a1.5 1.5 0 0 1 1.06.44l3.122 3.12A1.5 1.5 0 0 1 17 6.622V12.5a1.5 1.5 0 0 1-1.5 1.5h-1v-3.379a3 3 0 0 0-.879-2.121L10.5 5.379A3 3 0 0 0 8.379 4.5H7v-1Z" /><path d="M4.5 6A1.5 1.5 0 0 0 3 7.5v9A1.5 1.5 0 0 0 4.5 18h7a1.5 1.5 0 0 0 1.5-1.5v-5.879a1.5 1.5 0 0 0-.44-1.06L9.44 6.439A1.5 1.5 0 0 0 8.378 6H4.5Z" /></svg>
                                </x-gui::button>
                            </x-gui::tooltip>

                            <x-gui::tooltip text="Opens in a new tab" position="bottom">
                                <x-gui::button href="https://laravel.com" external variant="ghost" size="sm">Laravel</x-gui::button>
                            </x-gui::tooltip>
                            BLADE,
                    ],
                ],
                'props' => [
                    ['text', 'string', 'required', 'The tooltip text.'],
                    ['position', "'top' | 'bottom' | 'left' | 'right' | …", "'top'", 'Flips automatically when there is no room.'],
                ],
                'slots' => [
                    ['default', 'The trigger. Its first focusable element gets aria-describedby.'],
                ],
                'notes' => [
                    'Showing and hiding is CSS. Alpine adds aria-describedby and Escape-to-dismiss.',
                    'Touch screens can\'t hover, so never put essential information only in a tooltip.',
                ],
            ],

            'toasts' => [
                'name' => 'Toast',
                'group' => 'Overlays',
                'description' => 'Brief, non-blocking messages announced to screen readers. Place <x-gui::toasts /> once in your layout.',
                'usage' => <<<'BLADE'
                    {{-- In your layout --}}
                    <x-gui::toasts />

                    {{-- Anywhere --}}
                    <x-gui::button x-on:click="GiraffeUI.toast('Saved', { color: 'success' })">Save</x-gui::button>
                    BLADE,
                'examples' => [
                    [
                        'title' => 'Colours and titles',
                        'description' => 'Toasts dismiss after 5 seconds. Hovering or focusing one pauses it.',
                        'code' => <<<'BLADE'
                            <x-gui::button variant="outline" color="secondary" x-on:click="GiraffeUI.toast('Your changes were saved.', { color: 'success', title: 'Saved' })">Success</x-gui::button>
                            <x-gui::button variant="outline" color="secondary" x-on:click="GiraffeUI.toast('Check your connection and try again.', { color: 'danger', title: 'Upload failed', duration: 0 })">Error (stays)</x-gui::button>
                            <x-gui::button variant="outline" color="secondary" x-on:click="$dispatch('toast', { message: 'Sent with $dispatch' })">From Alpine</x-gui::button>
                            BLADE,
                    ],
                    [
                        'title' => 'Above a modal',
                        'description' => 'The region lives in the top layer, so toasts appear above open dialogs.',
                        'code' => <<<'BLADE'
                            <x-gui::button variant="outline" commandfor="toast-modal" command="show-modal">Open modal</x-gui::button>

                            <x-gui::modal id="toast-modal" title="Invite a teammate" size="sm">
                                <x-gui::field label="Email" name="invite"><x-gui::input type="email" value="grace@example.com" /></x-gui::field>
                                <x-slot:footer>
                                    <x-gui::modal.close>Close</x-gui::modal.close>
                                    <x-gui::button x-on:click="GiraffeUI.toast('Invite sent to grace@example.com', { color: 'success' })">Send invite</x-gui::button>
                                </x-slot:footer>
                            </x-gui::modal>
                            BLADE,
                    ],
                ],
                'props' => [
                    ['duration', 'int', '5000', 'Default time on screen, in milliseconds. 0 keeps toasts until dismissed.'],
                ],
                'slots' => [],
                'notes' => [
                    'Options for each toast: message, title, color (primary, secondary, success, warning, danger, info) and duration.',
                    'From Livewire: $this->dispatch(\'toast\', message: \'Saved\', color: \'success\').',
                    'After a redirect: session()->flash(\'toast\', \'Saved\') or an array of options.',
                    'While a modal is open, toasts still appear and are announced, but the browser makes everything outside the modal unclickable, so they dismiss on their own timer.',
                ],
            ],

            'accordion' => [
                'name' => 'Accordion',
                'group' => 'Disclosure',
                'description' => 'Sections that expand and collapse. Built on native <details>, so it works without JavaScript.',
                'preview' => 'block',
                'usage' => <<<'BLADE'
                    <x-gui::accordion exclusive>
                        <x-gui::accordion.item title="Question">Answer</x-gui::accordion.item>
                    </x-gui::accordion>
                    BLADE,
                'examples' => [
                    [
                        'title' => 'FAQ',
                        'description' => 'With exclusive, opening one item closes the others.',
                        'code' => <<<'BLADE'
                            <x-gui::accordion exclusive class="mx-auto max-w-lg">
                                <x-gui::accordion.item title="Is GiraffeUI free?" open>
                                    Yes. Every component is MIT licensed.
                                </x-gui::accordion.item>
                                <x-gui::accordion.item title="Do I need Livewire?">
                                    No. Components are plain Blade. Livewire works great with them, but it's optional.
                                </x-gui::accordion.item>
                                <x-gui::accordion.item title="Can I change the colours?">
                                    Yes. Override the CSS variables in your own theme. See the theming guide.
                                </x-gui::accordion.item>
                            </x-gui::accordion>
                            BLADE,
                    ],
                    [
                        'title' => 'Independent items',
                        'description' => 'Without exclusive, any number can be open.',
                        'code' => <<<'BLADE'
                            <x-gui::accordion class="mx-auto max-w-lg">
                                <x-gui::accordion.item title="Shipping" open>Orders ship within 2 business days.</x-gui::accordion.item>
                                <x-gui::accordion.item title="Returns" open>Return anything within 30 days.</x-gui::accordion.item>
                                <x-gui::accordion.item title="Warranty">Two years on all hardware.</x-gui::accordion.item>
                            </x-gui::accordion>
                            BLADE,
                    ],
                ],
                'props' => [
                    ['exclusive', 'bool', 'false', 'Only one item open at a time.'],
                    ['name', 'string', 'generated', 'The shared details name when exclusive.'],
                    ['title (item)', 'string', 'required', 'The item heading.'],
                    ['open (item)', 'bool', 'false', 'Start expanded.'],
                ],
                'slots' => [
                    ['default', 'Accordion items, or an item\'s content.'],
                ],
            ],

            'tabs' => [
                'name' => 'Tabs',
                'group' => 'Disclosure',
                'description' => 'Switch between panels of related content. Follows the WAI-ARIA tabs pattern. Needs Alpine, which Livewire includes.',
                'preview' => 'block',
                'usage' => <<<'BLADE'
                    <x-gui::tabs :tabs="['account' => 'Account', 'password' => 'Password']">
                        <x-gui::tabs.panel name="account">…</x-gui::tabs.panel>
                        <x-gui::tabs.panel name="password">…</x-gui::tabs.panel>
                    </x-gui::tabs>
                    BLADE,
                'examples' => [
                    [
                        'title' => 'Underline',
                        'description' => 'Arrow keys move between tabs, Home and End jump to the first and last, and Tab moves into the panel.',
                        'code' => <<<'BLADE'
                            <x-gui::tabs :tabs="['account' => 'Account', 'password' => 'Password', 'notifications' => 'Notifications']" class="mx-auto max-w-lg">
                                <x-gui::tabs.panel name="account">
                                    <x-gui::field label="Name" name="name"><x-gui::input value="Ada Lovelace" /></x-gui::field>
                                </x-gui::tabs.panel>
                                <x-gui::tabs.panel name="password">
                                    <x-gui::field label="New password" name="password"><x-gui::input type="password" /></x-gui::field>
                                </x-gui::tabs.panel>
                                <x-gui::tabs.panel name="notifications">
                                    <x-gui::switch name="digest" label="Weekly digest" checked />
                                </x-gui::tabs.panel>
                            </x-gui::tabs>
                            BLADE,
                    ],
                    [
                        'title' => 'Pills',
                        'code' => <<<'BLADE'
                            <x-gui::tabs variant="pills" :tabs="['monthly' => 'Monthly', 'yearly' => 'Yearly']" selected="yearly" class="mx-auto max-w-lg">
                                <x-gui::tabs.panel name="monthly"><p class="text-2xl font-semibold">$12 <span class="text-sm font-normal text-zinc-600 dark:text-zinc-400">/ month</span></p></x-gui::tabs.panel>
                                <x-gui::tabs.panel name="yearly"><p class="text-2xl font-semibold">$120 <span class="text-sm font-normal text-zinc-600 dark:text-zinc-400">/ year</span></p></x-gui::tabs.panel>
                            </x-gui::tabs>
                            BLADE,
                    ],
                ],
                'props' => [
                    ['tabs', 'array', '[]', 'name => label pairs, in order.'],
                    ['selected', 'string', 'first tab', 'The tab selected on load.'],
                    ['variant', "'underline' | 'pills'", "'underline'", ''],
                    ['id', 'string', 'generated', 'Prefix for tab and panel ids.'],
                    ['name (panel)', 'string', 'required', 'Matches a key in tabs.'],
                ],
                'slots' => [
                    ['default', 'The panels.'],
                ],
                'notes' => [
                    'Bind the selected tab with wire:model="tab" or x-model.',
                ],
            ],

            'table' => [
                'name' => 'Table',
                'group' => 'Data',
                'description' => 'Rows of data, from an array or composed cell by cell, with sorting, an empty state and pagination.',
                'preview' => 'block',
                'usage' => '<x-gui::table :columns="[\'name\', \'email\']" :rows="$users" />',
                'examples' => [
                    [
                        'title' => 'From data',
                        'description' => 'Pass a list of keys and they become headings automatically. Keys are read with data_get, so team.name works.',
                        'code' => <<<'BLADE'
                            <x-gui::table :columns="['name' => 'Full name', 'email' => 'Email', 'team.name' => 'Team']" :rows="$users" />
                            BLADE,
                    ],
                    [
                        'title' => 'Sorting',
                        'description' => 'Sortable headings link to ?sort=…&direction=… and set aria-sort. Sort the rows yourself (here, the catalog does it like a controller would). Try it.',
                        'code' => <<<'BLADE'
                            <x-gui::table
                                :columns="['name', 'email', 'role']"
                                :rows="$sortedUsers"
                                :sortable="['name', 'email', 'role']"
                                :sort="$sort"
                                :direction="$direction"
                            />
                            BLADE,
                    ],
                    [
                        'title' => 'Custom cells',
                        'description' => 'Compose the table for full control over each cell. With Livewire, put wire:click on a heading to sort with an action instead of a link.',
                        'code' => <<<'BLADE'
                            <x-gui::table>
                                <x-slot:head>
                                    <x-gui::table.heading>Member</x-gui::table.heading>
                                    <x-gui::table.heading>Role</x-gui::table.heading>
                                    <x-gui::table.heading><span class="sr-only">Actions</span></x-gui::table.heading>
                                </x-slot:head>

                                @foreach ($users as $user)
                                    <x-gui::table.row>
                                        <x-gui::table.cell>
                                            <div class="flex items-center gap-3">
                                                <x-gui::avatar :name="$user['name']" size="sm" />
                                                <div>
                                                    <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $user['name'] }}</p>
                                                    <p class="text-zinc-500 dark:text-zinc-400">{{ $user['email'] }}</p>
                                                </div>
                                            </div>
                                        </x-gui::table.cell>
                                        <x-gui::table.cell>
                                            <x-gui::badge :color="$user['role'] === 'Admin' ? 'primary' : 'secondary'">{{ $user['role'] }}</x-gui::badge>
                                        </x-gui::table.cell>
                                        <x-gui::table.cell class="text-right">
                                            <x-gui::button variant="ghost" size="sm">Edit</x-gui::button>
                                        </x-gui::table.cell>
                                    </x-gui::table.row>
                                @endforeach
                            </x-gui::table>
                            BLADE,
                    ],
                    [
                        'title' => 'Paginated',
                        'description' => 'Pass a paginator as rows and the table adds pagination for you.',
                        'code' => <<<'BLADE'
                            <x-gui::table :columns="['id' => '#', 'name', 'email', 'role']" :rows="$paginated" />
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
                    ['columns', 'array', 'null', "A list of keys (['name', 'email']) or a key => label map. Leave it out to compose the table."],
                    ['rows', 'iterable', '[]', 'Arrays, objects, models, a collection or a paginator.'],
                    ['sortable', 'array', '[]', 'Column keys whose headings sort.'],
                    ['sort', 'string', 'null', 'The column currently sorted.'],
                    ['direction', "'asc' | 'desc'", "'asc'", 'The current sort direction.'],
                    ['sortable (heading)', 'string', 'null', 'Makes a composed heading sortable by this key.'],
                    ['wrap (cell)', 'bool', 'false', 'Let a cell\'s text wrap.'],
                ],
                'slots' => [
                    ['head', 'Composed headings (table.heading).'],
                    ['default', 'Composed rows (table.row with table.cell).'],
                    ['empty', 'Shown when there are no rows. Defaults to "No results."'],
                    ['footer', 'Below the table. Defaults to pagination when rows is a paginator.'],
                ],
                'notes' => [
                    'On a sortable heading, wire:click and x-on:click go on the sort button; class and other attributes go on the <th>.',
                ],
            ],

            'pagination' => [
                'name' => 'Pagination',
                'group' => 'Data',
                'description' => 'Page links for any Laravel paginator, with a Livewire version.',
                'preview' => 'block',
                'usage' => '<x-gui::pagination :paginator="$users" />',
                'examples' => [
                    [
                        'title' => 'Links',
                        'code' => <<<'BLADE'
                            <x-gui::pagination :paginator="$paginated" />
                            BLADE,
                    ],
                ],
                'props' => [
                    ['paginator', 'Paginator', 'required', 'A length-aware or simple paginator.'],
                    ['livewire', 'bool', 'false', 'Use wire:click with Livewire\'s gotoPage, previousPage and nextPage instead of links.'],
                    ['window', 'int', '2', 'Pages shown either side of the current one.'],
                ],
                'slots' => [],
                'notes' => [
                    'Use it for every paginator: Paginator::defaultView(\'giraffeui::pagination.default\') in a service provider.',
                    'In Livewire, return \'giraffeui::pagination.livewire\' from paginationView(), or pass livewire to the component.',
                ],
            ],
        ];
    }
}
