<?php

use Illuminate\Support\Facades\Blade;
use Workbench\App\Catalog;
use Workbench\App\Providers\WorkbenchServiceProvider;

beforeEach(function () {
    $this->app->register(WorkbenchServiceProvider::class);
});

it('renders every catalog page', function (string $page) {
    $this->get("/catalog/{$page}")->assertOk();
})->with(fn () => [...array_keys(Catalog::guides()), ...array_keys(Catalog::components())]);

it('has no duplicate ids, so examples can target their own elements', function (string $page) {
    $dom = new DOMDocument;
    @$dom->loadHTML($this->get("/catalog/{$page}")->getContent(), LIBXML_NOERROR);

    $ids = array_map(fn (DOMElement $element) => $element->getAttribute('id'), iterator_to_array((new DOMXPath($dom))->query('//*[@id]')));

    expect(array_keys(array_filter(array_count_values($ids), fn ($count) => $count > 1)))->toBe([]);
})->with(fn () => [...array_keys(Catalog::guides()), ...array_keys(Catalog::components()), ...array_map(fn ($slug) => "examples/{$slug}", array_keys(Catalog::examples()))]);

it('returns 404 for unknown pages', function () {
    $this->get('/catalog/nope')->assertNotFound();
    $this->get('/catalog/examples/nope')->assertNotFound();
});

it('renders the full-page examples on their own, linked from the sidebar', function (string $example) {
    $this->get("/catalog/examples/{$example}")
        ->assertOk()
        ->assertDontSee('id="sidebar"', false)
        ->assertSee('Back to the catalog');

    $this->get('/catalog/introduction')->assertSee("href=\"/catalog/examples/{$example}\"", false);
})->with(fn () => array_keys(Catalog::examples()));

it('renders every example exactly as its code is written', function () {
    foreach (Catalog::components() as $component) {
        foreach ($component['examples'] as $example) {
            expect(Blade::render($example['code'], Catalog::data()))->not->toBeEmpty();
        }
    }
});

it('renders the playground with the requested props', function () {
    $this->get('/catalog/preview/button?variant=outline&color=danger&label=Delete&disabled=true')
        ->assertOk()
        ->assertSee('border-gui-danger-text', false)
        ->assertSee('Delete')
        ->assertSee('disabled', false);
});

it('ignores playground values the controls do not allow', function () {
    $this->get('/catalog/preview/button?variant=evil&size=%22%3E&label=%3Cscript%3E')
        ->assertOk()
        ->assertSee('bg-gui-primary', false)
        ->assertSee('h-10', false)
        ->assertDontSee('<script>', false);
});

it('validates the sign-in example like a real form, then signs in with a toast', function () {
    $this->from('/catalog/examples/sign-in')
        ->post('/catalog/examples/sign-in', ['email' => 'nope', 'password' => 'short'])
        ->assertRedirect('/catalog/examples/sign-in')
        ->assertSessionHasErrors(['email', 'password' => 'Passwords are at least 8 characters.'])
        ->assertSessionHasInput('email', 'nope');

    $this->post('/catalog/examples/sign-in', ['email' => 'ada@example.com', 'password' => 'hunter22'])
        ->assertRedirect('/catalog/examples/dashboard')
        ->assertSessionHas('gui-toast', ['message' => 'Signed in as ada@example.com.', 'color' => 'success']);
});

it('validates the register example, keeping everything but the passwords', function () {
    $this->from('/catalog/examples/register')
        ->post('/catalog/examples/register', ['name' => 'Ada Lovelace', 'email' => 'nope', 'password' => 'longenough', 'password_confirmation' => 'different'])
        ->assertRedirect('/catalog/examples/register')
        ->assertSessionHasErrors([
            'email',
            'password' => 'Add at least one number.',
            'password_confirmation' => "This doesn't match your password.",
            'terms' => 'Accept the terms to create an account.',
        ])
        ->assertSessionHasInput('name', 'Ada Lovelace')
        ->assertSessionMissing('_old_input.password');

    $this->post('/catalog/examples/register', ['name' => 'Ada Lovelace', 'email' => 'ada@example.com', 'password' => 'engine1843', 'password_confirmation' => 'engine1843', 'terms' => '1'])
        ->assertRedirect('/catalog/examples/dashboard')
        ->assertSessionHas('gui-toast', ['message' => 'Welcome to Acme, Ada.', 'color' => 'success']);
});

it('shows register errors in their fields, with the summary counting them', function () {
    $this->from('/catalog/examples/register')
        ->followingRedirects()
        ->post('/catalog/examples/register', ['name' => 'Ada', 'email' => 'ada@example.com', 'password' => 'engine1843', 'password_confirmation' => 'engine1843'])
        ->assertSee('Fix the field below')
        ->assertSee('Accept the terms to create an account.')
        ->assertSee('aria-describedby="gui-terms-fieldset-error"', false)
        ->assertSee('value="Ada"', false);
});

it('says a reset link was sent without revealing whether the account exists', function () {
    $this->from('/catalog/examples/forgot-password')
        ->post('/catalog/examples/forgot-password', ['email' => ''])
        ->assertSessionHasErrors('email');

    $this->from('/catalog/examples/forgot-password')
        ->followingRedirects()
        ->post('/catalog/examples/forgot-password', ['email' => 'ada@example.com'])
        ->assertSee('Check your email')
        ->assertSee('If ada@example.com has an Acme account')
        ->assertSee('value="ada@example.com"', false)
        ->assertSee('Send another link');
});

it('links the auth examples to each other', function () {
    $this->get('/catalog/examples/sign-in')->assertSee('href="/catalog/examples/register"', false)->assertSee('href="/catalog/examples/forgot-password"', false);
    $this->get('/catalog/examples/register')->assertSee('href="/catalog/examples/sign-in"', false);
    $this->get('/catalog/examples/forgot-password')->assertSee('href="/catalog/examples/sign-in"', false);
});

it('switches the pricing example between monthly and yearly prices without JavaScript', function () {
    $this->get('/catalog/examples/pricing')
        ->assertOk()
        ->assertSee('name="billing"', false)
        ->assertSee('value="monthly"', false)
        ->assertSee('group-has-[[value=yearly]:checked]/billing:hidden', false)
        ->assertSee('$99')
        ->assertSee('$79')
        ->assertSee('commandfor="contact-sales"', false)
        ->assertSee('<th scope="row"', false)
        ->assertSee('aria-label="Not included"', false);
});

it('searches timezones for the combobox demo as a JSON list', function () {
    $this->getJson('/catalog/api/timezones?q=tok')
        ->assertOk()
        ->assertJsonFragment(['value' => 'Asia/Tokyo', 'label' => 'Asia/Tokyo']);

    $this->getJson('/catalog/api/timezones?q=new york')->assertJsonFragment(['value' => 'America/New_York', 'label' => 'America/New York']);
    expect($this->getJson('/catalog/api/timezones')->json())->toHaveCount(20);
    expect($this->getJson('/catalog/api/timezones?q=nowhere-at-all')->json())->toBe([]);
});

describe('onboarding example', function () {
    it('walks through every step with real form posts, then creates the workspace', function () {
        $this->get('/catalog/examples/onboarding')->assertOk()->assertSee('Create your workspace')->assertSee('aria-current="step"', false);

        $this->post('/catalog/examples/onboarding', ['step' => 'workspace', 'workspace' => 'Analytical Engines', 'slug' => 'analytical-engines'])
            ->assertRedirect('/catalog/examples/onboarding?step=team');

        $this->get('/catalog/examples/onboarding?step=team')
            ->assertSee('Invite your team')
            // The finished step links back; later ones don't.
            ->assertSee('href="/catalog/examples/onboarding?step=workspace"', false)
            ->assertDontSee('href="/catalog/examples/onboarding?step=preferences"', false);

        $this->post('/catalog/examples/onboarding', ['step' => 'team', 'invites' => "grace@example.com, Grace@example.com\nalan@example.com", 'role' => 'admin'])
            ->assertRedirect('/catalog/examples/onboarding?step=preferences');
        $this->post('/catalog/examples/onboarding', ['step' => 'preferences', 'timezone' => 'Asia/Tokyo', 'digest' => '1'])
            ->assertRedirect('/catalog/examples/onboarding?step=review');

        $this->get('/catalog/examples/onboarding?step=review')
            ->assertSee('acme.app/analytical-engines')
            ->assertSee('grace@example.com, alan@example.com')
            ->assertSee('Asia/Tokyo')
            ->assertSee('Weekly summary');

        $this->post('/catalog/examples/onboarding', ['step' => 'review'])
            ->assertRedirect('/catalog/examples/dashboard')
            ->assertSessionHas('gui-toast', ['message' => 'Created the Analytical Engines workspace.', 'color' => 'success'])
            ->assertSessionMissing('onboarding');
    });

    it('validates each step on the server and keeps what was typed', function () {
        $this->from('/catalog/examples/onboarding')
            ->post('/catalog/examples/onboarding', ['step' => 'workspace', 'workspace' => 'Acme', 'slug' => 'Not Valid!'])
            ->assertSessionHasErrors(['slug' => 'Use lowercase letters, numbers and single dashes.'])
            ->assertSessionHasInput('workspace', 'Acme');

        $this->withSession(['onboarding' => ['workspace' => 'Acme', 'slug' => 'acme']])
            ->post('/catalog/examples/onboarding', ['step' => 'team', 'invites' => 'grace@example.com, nope', 'role' => 'member'])
            ->assertSessionHasErrors(['invites' => '"nope" isn\'t an email address.']);
    });

    it('never shows a step that has not been reached', function () {
        $this->get('/catalog/examples/onboarding?step=review')->assertSee('Create your workspace')->assertDontSee('Check everything');

        $this->withSession(['onboarding' => ['workspace' => 'Acme', 'slug' => 'acme']])
            ->get('/catalog/examples/onboarding?step=review')
            ->assertSee('Invite your team');

        $this->post('/catalog/examples/onboarding', ['step' => 'review'])->assertStatus(409);
        $this->post('/catalog/examples/onboarding', ['step' => 'nope'])->assertNotFound();
    });
});
