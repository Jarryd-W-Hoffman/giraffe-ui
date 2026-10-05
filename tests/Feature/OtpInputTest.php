<?php

it('is one real input set up for one-time codes, inside a field', function () {
    $html = (string) $this->blade('<x-gui::field label="Code" name="code" hint="Check your email."><x-gui::otp-input /></x-gui::field>');

    expect($html)
        ->toMatch('/<input(?=[^>]*type="text")(?=[^>]*name="code")(?=[^>]*id="gui-code")(?=[^>]*autocomplete="one-time-code")(?=[^>]*inputmode="numeric")(?=[^>]*maxlength="6")(?=[^>]*pattern="\[0-9\]\{6\}")(?=[^>]*aria-describedby="gui-code-hint")[^>]*>/s')
        ->toContain('aria-hidden="true"')
        ->and(substr_count($html, '<input'))->toBe(1)
        ->and(substr_count($html, 'x-bind:data-filled'))->toBe(6);
});

it('takes a length, letters and autosubmit', function () {
    $html = (string) $this->blade('<x-gui::otp-input name="pin" :length="4" alphanumeric autosubmit />');

    expect($html)->toContain('maxlength="4"', 'pattern="[A-Za-z0-9]{4}"', 'inputmode="text"', "'[^a-z0-9]'", 'requestSubmit')
        ->and(substr_count($html, 'x-bind:data-filled'))->toBe(4);
});

it('never refills the code from old input', function () {
    session()->put('_old_input', ['code' => '123456']);
    request()->setLaravelSession(session()->driver());

    expect((string) $this->blade('<x-gui::otp-input name="code" />'))->not->toContain('123456');
});

it('marks the boxes invalid with the field', function () {
    withErrors(['code' => 'That code has expired.']);

    expect((string) $this->blade('<x-gui::field label="Code" name="code"><x-gui::otp-input /></x-gui::field>'))
        ->toContain('aria-invalid="true"', 'group-has-aria-invalid/otp:border-gui-danger-text');
});
