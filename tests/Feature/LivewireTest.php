<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use Workbench\App\Livewire\AvatarUpload;
use Workbench\App\Livewire\CustomersTable;
use Workbench\App\Livewire\ProfileForm;
use Workbench\App\Livewire\SettingsTabs;
use Workbench\App\Livewire\SignupWizard;
use Workbench\App\Livewire\TimezonePicker;
use Workbench\App\Livewire\UsersTable;

pest()->group('livewire');

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
            ->assertDispatched('gui-toast', message: "Saved Ada Lovelace's profile.", color: 'success');
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

    it('binds a multiple segmented control to an array', function () {
        Livewire::test(SettingsTabs::class)
            ->assertSee('On the server: mon, thu')
            ->assertSeeHtml('name="digestDays[]"')
            ->set('digestDays', ['tue'])
            ->assertSee('On the server: tue')
            ->set('digestDays', [])
            ->assertSee('On the server: never');
    });
});

describe('customers table', function () {
    it('filters, sorts and paginates', function () {
        $table = Livewire::test(CustomersTable::class);

        expect($table->instance()->rows()->total())->toBe(42)
            ->and($table->instance()->rows()->count())->toBe(10);

        $table->set('status', 'churned');
        expect($table->instance()->rows()->pluck('status')->unique()->all())->toBe(['churned']);

        $table->set('status', 'all')->set('plan', 'enterprise')->call('sort', 'mrr')->call('sort', 'mrr');
        $mrr = $table->instance()->rows()->pluck('mrr')->all();
        expect($table->instance()->rows()->pluck('plan')->unique()->all())->toBe(['enterprise'])
            ->and($mrr)->toBe(collect($mrr)->sortDesc()->values()->all());

        $table->call('sort', 'evil')->assertSet('sortBy', 'mrr');
    });

    it('shows an empty state that clears the filters', function () {
        Livewire::test(CustomersTable::class)
            ->set('search', 'nobody-by-this-name')
            ->assertSee('No customers found')
            ->assertSee('Clear filters')
            ->call('clearFilters')
            ->assertSet('search', '')
            ->assertDontSee('No customers found');
    });

    it('changes the status of one customer or the selection', function () {
        $table = Livewire::test(CustomersTable::class)
            ->call('setStatus', 'churned', 'cus_1001')
            ->assertDispatched('gui-toast', color: 'success');

        expect($table->instance()->customers()->firstWhere('id', 'cus_1001')['status'])->toBe('churned');

        $table->set('selected', ['cus_1002', 'cus_1003'])
            ->call('setStatus', 'churned')
            ->assertDispatched('gui-toast', message: '2 customers marked churned.')
            ->call('setStatus', 'deleted-ish', 'cus_1004');

        expect($table->instance()->customers()->whereIn('id', ['cus_1002', 'cus_1003'])->pluck('status')->unique()->all())->toBe(['churned'])
            ->and($table->instance()->customers()->firstWhere('id', 'cus_1004')['status'])->not->toBe('deleted-ish');
    });

    it('asks before deleting, then deletes and drops them from the selection', function () {
        $table = Livewire::test(CustomersTable::class)
            ->set('selected', ['cus_1001', 'cus_1002', 'cus_1003'])
            ->call('confirmDelete', 'cus_1002')
            ->assertSet('deleting', ['cus_1002'])
            ->assertSee('Delete '.Livewire::test(CustomersTable::class)->instance()->customers()->firstWhere('id', 'cus_1002')['name'].'?')
            ->call('delete')
            ->assertSet('deleting', [])
            ->assertSet('selected', ['cus_1001', 'cus_1003']);

        expect($table->instance()->rows()->total())->toBe(41);

        $table->call('confirmDelete')
            ->assertSee('Delete 2 customers?')
            ->call('delete')
            ->assertDispatched('gui-toast', message: '2 customers deleted.')
            ->assertSet('selected', []);

        expect($table->instance()->rows()->total())->toBe(39);
    });

    it('shows only the columns ticked in the Columns menu', function () {
        Livewire::test(CustomersTable::class)
            ->assertSeeHtml('role="menuitemcheckbox"')
            ->assertSeeHtml('wire:model.live="columns"')
            ->assertSeeHtml("wire:click=\"sort('joined')\"")
            ->set('columns', ['plan', 'status'])
            ->assertDontSeeHtml("wire:click=\"sort('joined')\"")
            ->assertDontSeeHtml("wire:click=\"sort('mrr')\"");
    });

    it('ignores unknown ids and locks its bookkeeping', function () {
        Livewire::test(CustomersTable::class)
            ->call('confirmDelete', 'cus_nope')
            ->assertSet('deleting', []);

        Livewire::test(CustomersTable::class)->set('deleted', ['cus_1001']);
    })->throws(CannotUpdateLockedPropertyException::class);
});

describe('timezone picker', function () {
    it('binds the value with wire:model and searches through a plain route', function () {
        Livewire::test(TimezonePicker::class)
            ->assertSeeHtml('url: \'\\/catalog\\/api\\/timezones\'')
            ->assertDontSeeHtml('$wire')
            ->set('timezone', 'Asia/Tokyo')
            ->tap(fn ($picker) => expect(array_key_first($picker->instance()->initialTimezones()))->toBe('Asia/Tokyo'))
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('gui-toast', message: 'Saved Asia/Tokyo.')
            ->set('timezone', 'Mars/Olympus')
            ->call('save')
            ->assertHasErrors(['timezone' => 'timezone']);
    });
});

describe('wizard', function () {
    it('validates only the current step before moving on', function () {
        Livewire::test(SignupWizard::class)
            ->call('next')
            ->assertHasErrors(['name' => 'required', 'email' => 'required'])
            ->assertHasNoErrors('workspace')
            ->assertSet('step', 'account')
            ->set('name', 'Ada Lovelace')
            ->set('email', 'ada@example.com')
            ->call('next')
            ->assertHasNoErrors()
            ->assertSet('step', 'workspace')
            ->assertSeeHtml('wire:click="goTo(\'account\')"');
    });

    it('only jumps back to steps already reached', function () {
        Livewire::test(SignupWizard::class)
            ->call('goTo', 'review')
            ->assertSet('step', 'account')
            ->set('name', 'Ada Lovelace')
            ->set('email', 'ada@example.com')
            ->call('next')
            ->call('goTo', 'account')
            ->assertSet('step', 'account');
    });

    it('sends people back to the first step with a problem, then finishes', function () {
        $wizard = Livewire::test(SignupWizard::class)
            ->set('name', 'Ada Lovelace')
            ->set('email', 'ada@example.com')
            ->call('next')
            ->set('workspace', 'analytical-engine')
            ->call('next')
            ->assertSet('step', 'review')
            ->set('name', '')
            ->call('finish')
            ->assertSet('step', 'account')
            ->assertHasErrors('name')
            ->assertSee('The name field is required.');

        $wizard->set('name', 'Ada Lovelace')
            ->call('next')
            ->call('next')
            ->call('finish')
            ->assertDispatched('gui-toast', message: 'Created the analytical-engine workspace.', color: 'success')
            ->assertSet('step', 'account')
            ->assertSet('name', '');
    });

    it('locks the current step', function () {
        Livewire::test(SignupWizard::class)->set('step', 'review');
    })->throws(CannotUpdateLockedPropertyException::class);
});

describe('generated ids', function () {
    it('keeps generated ids stable across Livewire renders, so morphing keeps elements and focus', function () {
        $component = Livewire::test(SettingsTabs::class);
        preg_match('/id="(gui-tabs-[a-f0-9]{6}-\d+)-tab-account"/', $component->html(), $before);

        $component->set('digestDays', ['fri']);
        preg_match('/id="(gui-tabs-[a-f0-9]{6}-\d+)-tab-account"/', $component->html(), $after);

        expect($before[1] ?? null)->not->toBeNull()
            ->and($after[1] ?? null)->toBe($before[1]);
    });

    it('keeps generated ids random outside a Livewire component', function () {
        $a = (string) $this->blade('<x-gui::tooltip text="A"><x-gui::button>A</x-gui::button></x-gui::tooltip>');
        $b = (string) $this->blade('<x-gui::tooltip text="A"><x-gui::button>A</x-gui::button></x-gui::tooltip>');

        preg_match('/id="(gui-tooltip-[a-z0-9]+)"/', $a, $first);
        preg_match('/id="(gui-tooltip-[a-z0-9]+)"/', $b, $second);

        expect($first[1])->toMatch('/^gui-tooltip-[a-z0-9]{8}$/')
            ->and($first[1])->not->toBe($second[1]);
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
            ->assertDispatched('gui-toast', message: 'Uploaded ada.png.', color: 'success');
    });
});
