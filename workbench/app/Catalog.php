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
            'roles' => ['admin' => 'Admin', 'editor' => 'Editor', 'viewer' => 'Viewer'],
            // A self-contained "photo" so the catalog never depends on an image host.
            'photo' => 'data:image/svg+xml,'.rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 96 96"><defs><linearGradient id="g" x2="1" y2="1"><stop offset="0" stop-color="#f472b6"/><stop offset="1" stop-color="#6366f1"/></linearGradient></defs><rect width="96" height="96" fill="url(#g)"/><circle cx="48" cy="38" r="16" fill="#fff" fill-opacity=".85"/><path d="M16 96c4-20 18-30 32-30s28 10 32 30z" fill="#fff" fill-opacity=".85"/></svg>'),
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

            'table' => [
                'name' => 'Table',
                'group' => 'Data',
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
