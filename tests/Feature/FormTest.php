<?php

describe('field', function () {
    it('links the label, hint and error to the control', function () {
        withErrors(['email' => 'The email field is required.']);

        $html = (string) $this->blade('<x-gui::field label="Email" name="email" hint="We never share it."><x-gui::input type="email" /></x-gui::field>');

        expect(attr($html, 'label', 'for'))->toBe('gui-email')
            ->and(attr($html, 'input', 'id'))->toBe('gui-email')
            ->and(attr($html, 'input', 'name'))->toBe('email')
            ->and(attr($html, 'input', 'aria-invalid'))->toBe('true')
            ->and(attr($html, 'input', 'aria-describedby'))->toBe('gui-email-hint gui-email-error')
            ->and($html)->toContain('id="gui-email-hint"', 'We never share it.', 'id="gui-email-error"', 'The email field is required.');
    });

    it('is not invalid without errors', function () {
        $html = (string) $this->blade('<x-gui::field label="Email" name="email"><x-gui::input /></x-gui::field>');

        expect(attr($html, 'input', 'aria-invalid'))->toBeNull()
            ->and(attr($html, 'input', 'aria-describedby'))->toBeNull()
            ->and($html)->not->toContain('gui-email-error');
    });

    it('shows an explicit error', function () {
        $html = (string) $this->blade('<x-gui::field label="Name" name="name" error="Too short."><x-gui::input /></x-gui::field>');

        expect($html)->toContain('Too short.')
            ->and(attr($html, 'input', 'aria-invalid'))->toBe('true');
    });

    it('marks the label and control as required', function () {
        $html = (string) $this->blade('<x-gui::field label="Name" name="name" required><x-gui::input /></x-gui::field>');

        expect(attr($html, 'input', 'required'))->not->toBeNull()
            ->and($html)->toContain('aria-hidden="true"> *</span>');
    });

    it('turns array names into ids and error keys', function () {
        withErrors(['address.city' => 'Pick a city.']);

        $html = (string) $this->blade('<x-gui::field label="City" name="address[city]"><x-gui::input /></x-gui::field>');

        expect(attr($html, 'input', 'id'))->toBe('gui-address-city')
            ->and(attr($html, 'input', 'name'))->toBe('address[city]')
            ->and($html)->toContain('Pick a city.');
    });

    it('lets the control override the field id and name', function () {
        $html = (string) $this->blade('<x-gui::field label="Email" name="email"><x-gui::input id="custom" name="other" /></x-gui::field>');

        expect(attr($html, 'input', 'id'))->toBe('custom')
            ->and(attr($html, 'input', 'name'))->toBe('other');
    });

    it('does not pick up ids or names from other parent components', function () {
        $html = (string) $this->blade('<x-gui::card id="settings" name="card"><x-gui::input /></x-gui::card>');

        expect(attr($html, 'input', 'id'))->toBeNull()
            ->and(attr($html, 'input', 'name'))->toBeNull();
    });

    it('works with select and textarea too', function (string $control) {
        withErrors(['bio' => 'Required.']);

        $html = (string) $this->blade("<x-gui::field label=\"Bio\" name=\"bio\" hint=\"Short.\"><x-gui::{$control} /></x-gui::field>");

        expect(attr($html, $control, 'id'))->toBe('gui-bio')
            ->and(attr($html, $control, 'aria-describedby'))->toBe('gui-bio-hint gui-bio-error');
    })->with(['select', 'textarea']);
});

describe('input', function () {
    it('renders a text input by default', function () {
        $html = (string) $this->blade('<x-gui::input name="title" />');

        expect(attr($html, 'input', 'type'))->toBe('text')
            ->and(attr($html, 'input', 'id'))->toBe('gui-title')
            ->and(classesOf($html, 'input'))->toContain('border-gui-input', 'h-10');
    });

    it('is invalid when its own name has an error', function () {
        withErrors(['email' => 'Bad.']);

        $html = (string) $this->blade('<x-gui::input name="email" />');

        expect(attr($html, 'input', 'aria-invalid'))->toBe('true')
            ->and(attr($html, 'input', 'aria-describedby'))->toBeNull();
    });

    it('uses wire:model as the error key when there is no name', function () {
        withErrors(['form.email' => 'Bad.']);

        $html = (string) $this->blade('<x-gui::input wire:model="form.email" />');

        expect(attr($html, 'input', 'aria-invalid'))->toBe('true')
            ->and(attr($html, 'input', 'value'))->toBeNull();
    });

    it('can be forced valid or invalid', function () {
        withErrors(['a' => 'x']);

        expect(attr((string) $this->blade('<x-gui::input :invalid="true" />'), 'input', 'aria-invalid'))->toBe('true')
            ->and(attr((string) $this->blade('<x-gui::input name="a" :invalid="false" />'), 'input', 'aria-invalid'))->toBeNull();
    });

    it('keeps an explicit value and escapes it', function () {
        $html = (string) $this->blade('<x-gui::input name="q" value="&quot;><script>" />');

        expect($html)->not->toContain('<script>');
    });

    it('refills old input, except for passwords', function () {
        $this->app['session']->put('_old_input', ['email' => 'ada@example.com', 'password' => 'secret']);
        $this->app['request']->setLaravelSession($this->app['session']->driver());

        expect(attr((string) $this->blade('<x-gui::input name="email" />'), 'input', 'value'))->toBe('ada@example.com')
            ->and(attr((string) $this->blade('<x-gui::input type="password" name="password" />'), 'input', 'value'))->toBeNull();
    });

    it('wraps leading and trailing content', function () {
        $html = (string) $this->blade('<x-gui::input><x-slot:leading><svg data-icon></svg></x-slot:leading></x-gui::input>');

        expect($html)->toContain('data-icon')
            ->and(classesOf($html, 'input'))->toContain('pl-9');
    });

    it('passes attributes through to the input', function () {
        $html = (string) $this->blade('<x-gui::input wire:model.live="search" placeholder="Search" class="max-w-xs" />');

        expect($html)->toContain('wire:model.live="search"', 'placeholder="Search"')
            ->and(classesOf($html, 'input'))->toContain('max-w-xs');
    });
});

describe('textarea', function () {
    it('renders rows and slot content', function () {
        $html = (string) $this->blade('<x-gui::textarea name="bio" rows="5">Hello &amp; welcome</x-gui::textarea>');

        expect(attr($html, 'textarea', 'rows'))->toBe('5')
            ->and($html)->toContain('>Hello &amp; welcome</textarea>');
    });

    it('is a plain resizable textarea by default', function () {
        $html = (string) $this->blade('<x-gui::textarea name="bio" />');

        expect(classesOf($html, 'textarea'))->toContain('resize-y')->not->toContain('field-sizing-content')
            ->and(attr($html, 'textarea', 'style'))->toBeNull()
            ->and($html)->not->toContain('x-data');
    });

    it('grows with its content from a minimum of rows', function () {
        $html = (string) $this->blade('<x-gui::textarea name="bio" rows="2" autosize :max-rows="8" />');

        expect(classesOf($html, 'textarea'))->toContain('field-sizing-content', 'supports-[field-sizing:content]:resize-none')
            ->and(attr($html, 'textarea', 'style'))->toBe('min-height: calc(2lh + 1rem + 2px); max-height: calc(8lh + 1rem + 2px);');
    });

    it('keeps the developer\'s own style when autosizing', function () {
        $html = (string) $this->blade('<x-gui::textarea autosize style="color: red" />');

        expect(attr($html, 'textarea', 'style'))->toContain('min-height: calc(3lh + 1rem + 2px)', 'color: red');
    });

    it('counts characters against maxlength and describes the limit', function () {
        $html = (string) $this->blade('<x-gui::field label="Bio" name="bio" hint="Short."><x-gui::textarea counter maxlength="280">Café &amp; 🦒</x-gui::textarea></x-gui::field>');

        expect(attr($html, 'textarea', 'maxlength'))->toBe('280')
            ->and(attr($html, 'textarea', 'aria-describedby'))->toBe('gui-bio-hint gui-bio-limit')
            ->and($html)->toContain('id="gui-bio-limit"', 'Up to 280 characters.', 'aria-live="polite"')
            // "Café & 🦒" is 9 UTF-16 code units, which is what the browser counts.
            ->and($html)->toContain('length: 9,', '<span x-text="length">9</span> / 280');
    });

    it('counts without a limit', function () {
        $html = (string) $this->blade('<x-gui::textarea aria-label="Notes" counter />');

        expect(attr($html, 'textarea', 'id'))->toStartWith('gui-textarea-')
            ->and(attr($html, 'textarea', 'aria-describedby'))->toBeNull()
            ->and($html)->toContain('max: null', '<span x-text="say(\'character\', \'characters\', length)">0 characters</span>')
            ->not->toContain('Up to');
    });
});

describe('select', function () {
    /**
     * @return list<array{value: string, label: string, selected: bool, disabled: bool}>
     */
    function options(string $html): array
    {
        $dom = new DOMDocument;
        @$dom->loadHTML('<?xml encoding="utf-8"?>'.$html, LIBXML_NOERROR);

        return array_map(fn (DOMElement $option) => [
            'value' => $option->getAttribute('value'),
            'label' => trim($option->textContent),
            'selected' => $option->hasAttribute('selected'),
            'disabled' => $option->hasAttribute('disabled'),
        ], iterator_to_array($dom->getElementsByTagName('option')));
    }

    it('renders options from an array and marks the value selected', function () {
        $html = (string) $this->blade('<x-gui::select name="role" :options="[\'admin\' => \'Admin\', \'editor\' => \'Editor\']" value="editor" />');

        expect(options($html))->toBe([
            ['value' => 'admin', 'label' => 'Admin', 'selected' => false, 'disabled' => false],
            ['value' => 'editor', 'label' => 'Editor', 'selected' => true, 'disabled' => false],
        ])->and(attr($html, 'select', 'value'))->toBeNull();
    });

    it('adds a placeholder option selected when there is no value', function () {
        $html = (string) $this->blade('<x-gui::select placeholder="Choose a role"><option value="a">A</option></x-gui::select>');

        expect(options($html))->toBe([
            ['value' => '', 'label' => 'Choose a role', 'selected' => true, 'disabled' => true],
            ['value' => 'a', 'label' => 'A', 'selected' => false, 'disabled' => false],
        ]);
    });
});

describe('checkbox and radio', function () {
    it('links the label and description', function (string $type) {
        $html = (string) $this->blade("<x-gui::{$type} name=\"plan\" value=\"pro\" label=\"Pro\" description=\"For teams.\" />");

        expect(attr($html, 'input', 'type'))->toBe($type)
            ->and(attr($html, 'input', 'id'))->toBe('gui-plan-pro')
            ->and(attr($html, 'label', 'for'))->toBe('gui-plan-pro')
            ->and(attr($html, 'input', 'aria-describedby'))->toBe('gui-plan-pro-description')
            ->and($html)->toContain('For teams.');
    })->with(['checkbox', 'radio']);

    it('gives every option in a group its own id', function () {
        $html = (string) $this->blade('<x-gui::checkbox name="roles[]" value="admin" /><x-gui::checkbox name="roles[]" value="editor" />');

        expect($html)->toContain('id="gui-roles-admin"', 'id="gui-roles-editor"');
    });

    it('is invalid when the group has an error', function () {
        withErrors(['roles' => 'Pick one.']);

        $html = (string) $this->blade('<x-gui::checkbox name="roles[]" value="admin" />');

        expect(attr($html, 'input', 'aria-invalid'))->toBe('true');
    });

    it('uses the slot as the label', function () {
        $html = (string) $this->blade('<x-gui::checkbox name="terms">I agree to the <a href="/terms">terms</a></x-gui::checkbox>');

        expect($html)->toContain('<a href="/terms">terms</a></label>');
    });

    it('passes checked and wire:model through', function () {
        $html = (string) $this->blade('<x-gui::checkbox wire:model="remember" checked />');

        expect($html)->toContain('wire:model="remember"')
            ->and(attr($html, 'input', 'checked'))->not->toBeNull();
    });
});

describe('switch', function () {
    it('is a checkbox with the switch role', function () {
        $html = (string) $this->blade('<x-gui::switch name="notifications" label="Email me" description="Weekly digest." />');

        expect(attr($html, 'input', 'type'))->toBe('checkbox')
            ->and(attr($html, 'input', 'role'))->toBe('switch')
            ->and(attr($html, 'label', 'for'))->toBe('gui-notifications')
            ->and(attr($html, 'input', 'aria-describedby'))->toBe('gui-notifications-description');
    });
});

describe('fieldset', function () {
    it('renders a legend, hint and group error', function () {
        withErrors(['plan' => 'Choose a plan.']);

        $html = (string) $this->blade('<x-gui::fieldset legend="Plan" hint="Change any time." name="plan"><x-gui::radio name="plan" value="free" label="Free" /></x-gui::fieldset>');

        expect($html)->toContain('<legend', 'Plan', 'Change any time.', 'Choose a plan.')
            ->and(attr($html, 'fieldset', 'aria-describedby'))->toBe('gui-plan-fieldset-hint gui-plan-fieldset-error');
    });
});

describe('radio card', function () {
    it('makes the whole card the label', function () {
        $html = (string) $this->blade('<x-gui::radio variant="card" name="size" value="small" label="Small" description="1 CPU" checked />');

        expect($html)->toMatch('/^\s*<label for="gui-size-small"[^>]*>\s*<input type="radio"/')
            ->and(classesOf($html, 'label'))->toContain('has-checked:border-gui-primary-text', 'has-focus-visible:outline-2', 'has-aria-invalid:border-gui-danger-text')
            ->and(attr($html, 'input', 'checked'))->not->toBeNull()
            ->and(attr($html, 'input', 'aria-describedby'))->toBe('gui-size-small-description')
            ->and(attr($html, 'input', 'variant'))->toBeNull()
            ->and($html)->toContain('<span id="gui-size-small-description"', '1 CPU')
            ->not->toContain('<p ', '<div');
    });

    it('marks the card invalid from the group error', function () {
        withErrors(['size' => 'Pick a size.']);

        $html = (string) $this->blade('<x-gui::radio variant="card" name="size" value="small" label="Small" />');

        expect(attr($html, 'input', 'aria-invalid'))->toBe('true');
    });
});

describe('segmented control', function () {
    $options = ['list' => 'List', 'grid' => 'Grid'];

    it('renders a labelled radiogroup of native radios', function () use ($options) {
        $html = (string) $this->blade('<x-gui::segmented-control name="view" label="View" :options="$options" value="grid" class="mt-4" />', ['options' => $options]);

        expect(attr($html, 'div', 'role'))->toBe('radiogroup')
            ->and(attr($html, 'div', 'aria-label'))->toBe('View')
            ->and(classesOf($html, 'div'))->toContain('mt-4', 'inline-flex')
            ->and(substr_count($html, 'type="radio"'))->toBe(2)
            ->and(substr_count($html, 'name="view"'))->toBe(2)
            ->and($html)->toMatch('/value="grid"[^>]*checked/')
            ->not->toMatch('/value="list"[^>]*checked/');
    });

    it('puts wire:model, disabled and required on every radio', function () use ($options) {
        $html = (string) $this->blade('<x-gui::segmented-control wire:model.live="view" disabled required aria-labelledby="view-label" :options="$options" />', ['options' => $options]);

        expect(substr_count($html, 'wire:model.live="view"'))->toBe(2)
            ->and(substr_count($html, 'name="view"'))->toBe(2)
            ->and(substr_count($html, 'disabled="disabled"'))->toBe(2)
            ->and(substr_count($html, 'required="required"'))->toBe(2)
            ->and(attr($html, 'div', 'aria-labelledby'))->toBe('view-label')
            ->and(attr($html, 'div', 'disabled'))->toBeNull()
            ->and($html)->not->toMatch('/\schecked[=\s>]/');
    });

    it('refills old input and shows errors', function () use ($options) {
        withErrors(['view' => 'Pick one.']);
        $this->app['session']->put('_old_input', ['view' => 'list']);
        $this->app['request']->setLaravelSession($this->app['session']->driver());

        $html = (string) $this->blade('<x-gui::segmented-control name="view" label="View" :options="$options" />', ['options' => $options]);

        expect(attr($html, 'div', 'aria-invalid'))->toBe('true')
            ->and($html)->toMatch('/value="list"[^>]*checked/');
    });

    it('allows several with multiple', function () {
        $days = ['mon' => 'Mon', 'tue' => 'Tue', 'wed' => 'Wed'];
        $html = (string) $this->blade('<x-gui::segmented-control name="days" label="Days" multiple required :options="$days" :value="[\'mon\', \'wed\']" />', ['days' => $days]);

        expect(attr($html, 'div', 'role'))->toBe('group')
            ->and(substr_count($html, 'type="checkbox"'))->toBe(3)
            ->and(substr_count($html, 'name="days[]"'))->toBe(3)
            ->and($html)->toMatch('/value="mon"[^>]*checked/')
            ->toMatch('/value="wed"[^>]*checked/')
            ->not->toMatch('/value="tue"[^>]*checked/')
            ->not->toContain('required');
    });

    it('refills several from old input and flags item errors', function () {
        withErrors(['days.1' => 'Not a day.']);
        $this->app['session']->put('_old_input', ['days' => ['tue']]);
        $this->app['request']->setLaravelSession($this->app['session']->driver());

        $html = (string) $this->blade('<x-gui::segmented-control name="days" label="Days" multiple :options="[\'mon\' => \'Mon\', \'tue\' => \'Tue\']" />');

        expect(attr($html, 'div', 'aria-invalid'))->toBe('true')
            ->and($html)->toMatch('/value="tue"[^>]*checked/')
            ->not->toMatch('/value="mon"[^>]*checked/');
    });

    it('binds several to a wire:model array', function () {
        $html = (string) $this->blade('<x-gui::segmented-control wire:model.live="days" label="Days" multiple :options="[\'mon\' => \'Mon\', \'tue\' => \'Tue\']" />');

        expect(substr_count($html, 'name="days[]"'))->toBe(2)
            ->and(substr_count($html, 'wire:model.live="days"'))->toBe(2);
    });

    it('gives unnamed radios a shared generated name', function () use ($options) {
        $html = (string) $this->blade('<x-gui::segmented-control label="View" :options="$options" full />', ['options' => $options]);

        preg_match_all('/name="(gui-segmented-[a-z0-9]{8})"/', $html, $names);

        expect($names[1])->toHaveCount(2)
            ->and(array_unique($names[1]))->toHaveCount(1)
            ->and(classesOf($html, 'div'))->toContain('flex', 'w-full');
    });
});

describe('label', function () {
    it('shows a required marker hidden from screen readers', function () {
        $html = (string) $this->blade('<x-gui::label for="x" required>Name</x-gui::label>');

        expect($html)->toContain('for="x"', '<span class="text-gui-danger-text" aria-hidden="true"> *</span>');
    });
});
