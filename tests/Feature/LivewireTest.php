<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Workbench\App\Livewire\AvatarUpload;
use Workbench\App\Livewire\ProfileForm;
use Workbench\App\Livewire\SettingsTabs;
use Workbench\App\Livewire\UsersTable;

describe('forms', function () {
    it('shows validation errors in the field and marks the input invalid', function () {
        Livewire::test(ProfileForm::class)
            ->call('save')
            ->assertHasErrors(['name' => 'required', 'email' => 'required', 'country' => 'required'])
            ->assertSee('Choose a country.')
            ->assertSeeHtml('id="gui-name-error"')
            ->assertSeeHtml('aria-describedby="gui-name-error"')
            ->assertSeeHtml('aria-invalid="true"');
    });

    it('clears errors and dispatches a toast once valid', function () {
        Livewire::test(ProfileForm::class)
            ->set('name', 'Ada Lovelace')
            ->set('email', 'ada@example.com')
            ->set('country', 'gb')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDontSeeHtml('aria-invalid="true"')
            ->assertDispatched('toast', message: "Saved Ada Lovelace's profile.", color: 'success');
    });

    it('validates the birthday from the date picker', function () {
        Livewire::test(ProfileForm::class)
            ->set('birthday', '2999-01-01')
            ->call('save')
            ->assertHasErrors(['birthday' => 'before'])
            ->assertSeeHtml('Your birthday must be in the past.');
    });

    it('does not refill wire:model inputs from old input', function () {
        $html = Livewire::test(ProfileForm::class)->html();

        expect($html)->toMatch('/<input(?=[^>]*wire:model="name")(?![^>]*\svalue=)[^>]*>/s')
            ->toMatch('/<input(?=[^>]*wire:model="email")(?![^>]*\svalue=)[^>]*>/s');
    });
});

describe('tables', function () {
    it('sorts with wire:click and marks the sorted column', function () {
        Livewire::test(UsersTable::class)
            ->assertSeeHtml('aria-sort="ascending"')
            ->call('sort', 'name')
            ->assertSet('sortDirection', 'desc')
            ->assertSeeHtml('aria-sort="descending"')
            ->call('sort', 'role')
            ->assertSet('sortBy', 'role')
            ->assertSet('sortDirection', 'asc');
    });

    it('paginates with Livewire actions', function () {
        Livewire::test(UsersTable::class)
            ->assertSeeHtml("wire:click=\"gotoPage(2, 'page')\"")
            ->call('gotoPage', 2)
            ->assertSeeHtml('aria-current="page"><span class="sr-only">Page </span>2');
    });

    it('searches and shows the empty state', function () {
        Livewire::test(UsersTable::class)
            ->set('search', 'Grace')
            ->assertSee('Grace Hopper')
            ->assertDontSee('Alan Turing')
            ->set('search', 'nobody at all')
            ->assertSee('Nobody matches');
    });
});

describe('tabs', function () {
    it('renders the server-side tab as selected and follows changes', function () {
        Livewire::test(SettingsTabs::class)
            ->assertSeeHtml('x-modelable="selected"')
            ->assertSeeHtml('wire:model.live="tab"')
            ->set('tab', 'billing')
            ->assertSeeHtmlInOrder(['-tab-billing"', 'aria-selected="true"']);
    });
});

describe('uploads', function () {
    it('validates and uploads an image', function () {
        Storage::fake('local');

        Livewire::test(AvatarUpload::class)
            ->call('save')
            ->assertHasErrors(['avatar' => 'required'])
            ->assertSee('Choose an image to upload.')
            ->set('avatar', UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf'))
            ->call('save')
            ->assertHasErrors(['avatar' => 'image'])
            ->set('avatar', UploadedFile::fake()->image('ada.png'))
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('toast', message: 'Uploaded ada.png.', color: 'success');
    });
});
