<?php

it('is a radio group named by its field, one radio per star', function () {
    $html = (string) $this->blade('<x-gui::field label="Your rating" name="stars" required><x-gui::rating value="3" /></x-gui::field>');

    expect($html)
        ->toMatch('/role="radiogroup"\s+id="gui-stars"\s+aria-labelledby="gui-stars-label"/')
        ->and(substr_count($html, 'type="radio"'))->toBe(5)
        ->and(substr_count($html, 'name="stars"'))->toBe(5)
        ->and(substr_count($html, 'required="required"'))->toBe(5)
        ->and($html)->toContain('aria-label="1 star"', 'aria-label="3 stars"')
        ->toMatch('/value="3"\s+aria-label="3 stars"\s+checked/');
});

it('refills from old input, and binds with wire:model on every radio', function () {
    session()->put('_old_input', ['stars' => '4']);
    request()->setLaravelSession(session()->driver());

    expect((string) $this->blade('<x-gui::rating name="stars" />'))->toMatch('/value="4"\s+aria-label="4 stars"\s+checked/');
    expect(substr_count((string) $this->blade('<x-gui::rating wire:model.live="stars" :max="10" />'), 'wire:model.live="stars"'))->toBe(10);
});

it('shows a read-only rating with partial stars', function () {
    $html = (string) $this->blade('<x-gui::rating readonly :value="3.5" />');

    expect($html)->toContain('role="img"', 'aria-label="Rated 3.5 out of 5"', 'style="width: 50%"')
        ->not->toContain('type="radio"')
        ->and(substr_count($html, 'style="width: 100%"'))->toBe(3);
});

it('marks the group invalid with the field', function () {
    withErrors(['stars' => 'Choose a rating.']);

    expect((string) $this->blade('<x-gui::field label="Stars" name="stars"><x-gui::rating /></x-gui::field>'))
        ->toMatch('/role="radiogroup"[^>]*aria-invalid="true"/');
});
