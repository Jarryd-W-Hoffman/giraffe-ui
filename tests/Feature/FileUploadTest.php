<?php

it('wraps a real, labelled file input in a drop zone', function () {
    $html = (string) $this->blade('<x-gui::file-upload name="avatar" accept="image/*" hint="PNG or JPG, up to 2 MB" />');

    expect($html)
        ->toMatch('/<input(?=[^>]*type="file")(?=[^>]*id="gui-avatar")(?=[^>]*name="avatar")(?=[^>]*accept="image\/\*")(?=[^>]*aria-describedby="gui-avatar-zone-hint")(?=[^>]*opacity-0)[^>]*>/s')
        ->toContain('Choose a file', 'id="gui-avatar-zone-hint"', 'PNG or JPG, up to 2 MB')
        ->not->toContain('<label');
});

it('words itself for multiple files', function () {
    expect((string) $this->blade('<x-gui::file-upload name="photos[]" multiple />'))->toContain('Choose files', 'drag them here', 'multiple');
});

it('works inside a field, with errors', function () {
    withErrors(['avatar' => 'The avatar must be an image.']);

    $html = (string) $this->blade('<x-gui::field label="Avatar" name="avatar" hint="Square works best."><x-gui::file-upload /></x-gui::field>');

    expect($html)->toMatch('/<input(?=[^>]*type="file")(?=[^>]*id="gui-avatar")(?=[^>]*aria-invalid="true")(?=[^>]*aria-describedby="gui-avatar-hint gui-avatar-error")[^>]*>/s')
        ->toContain('The avatar must be an image.')
        ->and(substr_count($html, '<label'))->toBe(1);
});

it('passes wire:model to the input and listens for Livewire upload progress', function () {
    $html = (string) $this->blade('<x-gui::file-upload wire:model="avatar" aria-label="Avatar" />');

    expect($html)->toMatch('/<input(?=[^>]*type="file")(?=[^>]*wire:model="avatar")[^>]*>/s')
        ->toContain('x-on:livewire-upload-progress', 'role="progressbar"', 'x-on:livewire-upload-error');
});
