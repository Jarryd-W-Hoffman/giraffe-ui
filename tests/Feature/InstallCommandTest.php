<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use JayAitch\GiraffeUI\Console\InstallCommand;

const LIVEWIRE_QUESTION = 'Would you like to install Livewire? GiraffeUI works with or without it.';
const LAYOUT_QUESTION = 'Would you like to publish the GiraffeUI app layout (with dark mode)?';
const LAYOUT_EXISTS_QUESTION = 'A layout already exists at resources/views/components/layouts/app.blade.php. What would you like to do?';

beforeEach(function () {
    $this->originalBasePath = $this->app->basePath();
    $this->basePath = sys_get_temp_dir().'/giraffeui-'.bin2hex(random_bytes(6));

    File::ensureDirectoryExists($this->basePath.'/resources/css');
    $this->app->setBasePath($this->basePath);

    $this->css = fn () => File::get($this->basePath.'/resources/css/app.css');
    $this->layout = fn (string $name = 'app') => $this->basePath."/resources/views/components/layouts/{$name}.blade.php";
});

afterEach(function () {
    $this->app->setBasePath($this->originalBasePath);
    File::deleteDirectory($this->basePath);
});

function writeCss(string $css): void
{
    File::put(base_path('resources/css/app.css'), $css);
}

it('imports the CSS after Tailwind, publishes the layout and enables class-based dark mode', function () {
    writeCss("@import 'tailwindcss';\n\n@theme {\n    --font-sans: 'Inter';\n}\n");

    $this->artisan('gui:install')
        ->expectsConfirmation(LIVEWIRE_QUESTION, 'no')
        ->expectsConfirmation(LAYOUT_QUESTION, 'yes')
        ->assertSuccessful();

    expect(($this->css)())->toBe(
        "@import 'tailwindcss';\n".InstallCommand::CSS_IMPORT."\n\n@theme {\n    --font-sans: 'Inter';\n}\n\n".InstallCommand::DARK_VARIANT."\n"
    );
    expect(File::get(($this->layout)()))->toBe(File::get(__DIR__.'/../../stubs/layout.blade.php'));
});

it('is safe to run more than once', function () {
    writeCss("@import \"tailwindcss\";\n");

    foreach ([1, 2] as $run) {
        $this->artisan('gui:install')
            ->expectsConfirmation(LIVEWIRE_QUESTION, 'no')
            ->expectsConfirmation(LAYOUT_QUESTION, $run === 1 ? 'yes' : 'no')
            ->assertSuccessful();
    }

    expect(substr_count(($this->css)(), 'giraffeui.css'))->toBe(1)
        ->and(substr_count(($this->css)(), '@custom-variant dark'))->toBe(1);
});

it('leaves an existing dark variant alone', function () {
    $css = "@import 'tailwindcss';\n\n@custom-variant dark (&:is(.dark *));\n";
    writeCss($css);

    $this->artisan('gui:install')
        ->expectsConfirmation(LIVEWIRE_QUESTION, 'no')
        ->expectsConfirmation(LAYOUT_QUESTION, 'yes')
        ->assertSuccessful();

    expect(substr_count(($this->css)(), '@custom-variant dark'))->toBe(1);
});

it('does not touch the CSS when Tailwind v4 is not set up', function () {
    writeCss("@tailwind base;\n@tailwind components;\n@tailwind utilities;\n");

    $this->artisan('gui:install')
        ->expectsOutputToContain('GiraffeUI needs Tailwind CSS v4')
        ->expectsConfirmation(LIVEWIRE_QUESTION, 'no')
        ->expectsConfirmation(LAYOUT_QUESTION, 'no')
        ->assertSuccessful();

    expect(($this->css)())->toBe("@tailwind base;\n@tailwind components;\n@tailwind utilities;\n");
});

it('explains what to add when there is no app.css', function () {
    $this->artisan('gui:install')
        ->expectsOutputToContain("Couldn't find resources/css/app.css")
        ->expectsConfirmation(LIVEWIRE_QUESTION, 'no')
        ->expectsConfirmation(LAYOUT_QUESTION, 'yes')
        ->assertSuccessful();

    expect(File::exists(($this->layout)()))->toBeTrue()
        ->and(File::exists($this->basePath.'/resources/css/app.css'))->toBeFalse();
});

it('can install the layout under a different name without touching the existing one', function () {
    writeCss("@import 'tailwindcss';\n");
    File::ensureDirectoryExists(dirname(($this->layout)()));
    File::put(($this->layout)(), 'my layout');

    $this->artisan('gui:install')
        ->expectsConfirmation(LIVEWIRE_QUESTION, 'no')
        ->expectsConfirmation(LAYOUT_QUESTION, 'yes')
        ->expectsChoice(LAYOUT_EXISTS_QUESTION, 'Install under a different name', ['Install under a different name', 'Overwrite', 'Skip'])
        ->expectsQuestion('What should the layout be called?', 'Admin Panel')
        ->assertSuccessful();

    expect(File::get(($this->layout)()))->toBe('my layout')
        ->and(File::exists(($this->layout)('admin-panel')))->toBeTrue();
});

it('refuses a different name that is already taken', function () {
    writeCss("@import 'tailwindcss';\n");
    File::ensureDirectoryExists(dirname(($this->layout)()));
    File::put(($this->layout)(), 'my layout');
    File::put(($this->layout)('gui'), 'another layout');

    $this->artisan('gui:install')
        ->expectsConfirmation(LIVEWIRE_QUESTION, 'no')
        ->expectsConfirmation(LAYOUT_QUESTION, 'yes')
        ->expectsChoice(LAYOUT_EXISTS_QUESTION, 'Install under a different name', ['Install under a different name', 'Overwrite', 'Skip'])
        ->expectsQuestion('What should the layout be called?', 'gui')
        ->expectsOutputToContain('already taken')
        ->assertSuccessful();

    expect(File::get(($this->layout)('gui')))->toBe('another layout');
});

it('can overwrite or skip an existing layout', function (string $choice, bool $overwritten) {
    writeCss("@import 'tailwindcss';\n");
    File::ensureDirectoryExists(dirname(($this->layout)()));
    File::put(($this->layout)(), 'my layout');

    $this->artisan('gui:install')
        ->expectsConfirmation(LIVEWIRE_QUESTION, 'no')
        ->expectsConfirmation(LAYOUT_QUESTION, 'yes')
        ->expectsChoice(LAYOUT_EXISTS_QUESTION, $choice, ['Install under a different name', 'Overwrite', 'Skip'])
        ->assertSuccessful();

    expect(File::get(($this->layout)()) === 'my layout')->toBe(! $overwritten);
})->with([
    'overwrite' => ['Overwrite', true],
    'skip' => ['Skip', false],
]);

it('installs Livewire with Composer when asked', function () {
    Process::fake();
    writeCss("@import 'tailwindcss';\n");

    $this->artisan('gui:install')
        ->expectsConfirmation(LIVEWIRE_QUESTION, 'yes')
        ->expectsConfirmation(LAYOUT_QUESTION, 'no')
        ->assertSuccessful();

    Process::assertRan(fn ($process) => $process->command === ['composer', 'require', 'livewire/livewire']
        && $process->path === $this->basePath);
});

it('skips the Livewire question when the app already requires it', function () {
    Process::fake();
    writeCss("@import 'tailwindcss';\n");
    File::put($this->basePath.'/composer.json', json_encode(['require' => ['livewire/livewire' => '^4.0']]));

    $this->artisan('gui:install')
        ->expectsOutputToContain('ALREADY INSTALLED')
        ->expectsConfirmation(LAYOUT_QUESTION, 'no')
        ->assertSuccessful();

    Process::assertNothingRan();
});

it('reports a failed Livewire install', function () {
    Process::fake(['*' => Process::result(exitCode: 1)]);
    writeCss("@import 'tailwindcss';\n");

    $this->artisan('gui:install')
        ->expectsConfirmation(LIVEWIRE_QUESTION, 'yes')
        ->expectsOutputToContain('Livewire could not be installed')
        ->expectsConfirmation(LAYOUT_QUESTION, 'no')
        ->assertSuccessful();
});

it('uses sensible defaults with --no-interaction', function () {
    Process::fake();
    writeCss("@import 'tailwindcss';\n");

    $exitCode = $this->withoutMockingConsoleOutput()->artisan('gui:install', ['--no-interaction' => true]);

    expect($exitCode)->toBe(0);

    Process::assertNothingRan();
    expect(($this->css)())->toContain(InstallCommand::CSS_IMPORT)
        ->and(File::exists(($this->layout)()))->toBeTrue();
});

const ALPINE_QUESTION = 'Would you like to start Alpine in resources/js/app.js? GiraffeUI uses it for tabs, menus, the combobox and toasts.';

function writeJs(string $js, ?array $package = null): void
{
    File::ensureDirectoryExists(base_path('resources/js'));
    File::put(base_path('resources/js/app.js'), $js);

    if ($package !== null) {
        File::put(base_path('package.json'), json_encode($package));
    }
}

it('starts Alpine in app.js without Livewire, and says to install it', function () {
    writeCss("@import 'tailwindcss';\n");
    writeJs("import './bootstrap';\n");

    $this->artisan('gui:install')
        ->expectsConfirmation(LIVEWIRE_QUESTION, 'no')
        ->expectsConfirmation(ALPINE_QUESTION, 'yes')
        ->expectsOutputToContain('npm install alpinejs')
        ->expectsConfirmation(LAYOUT_QUESTION, 'no')
        ->assertSuccessful();

    expect(File::get(base_path('resources/js/app.js')))->toBe("import './bootstrap';\n\n".InstallCommand::ALPINE_SETUP."\n");

    // Running it again leaves app.js alone.
    $this->artisan('gui:install')
        ->expectsConfirmation(LIVEWIRE_QUESTION, 'no')
        ->expectsOutputToContain('ALREADY SET UP')
        ->expectsConfirmation(LAYOUT_QUESTION, 'no')
        ->assertSuccessful();

    expect(substr_count(File::get(base_path('resources/js/app.js')), 'alpinejs'))->toBe(1);
});

it('skips the npm reminder when alpinejs is already a dependency', function () {
    writeCss("@import 'tailwindcss';\n");
    writeJs("import './bootstrap';\n", ['devDependencies' => ['alpinejs' => '^3.0']]);

    $this->artisan('gui:install')
        ->expectsConfirmation(LIVEWIRE_QUESTION, 'no')
        ->expectsConfirmation(ALPINE_QUESTION, 'yes')
        ->doesntExpectOutputToContain('npm install alpinejs')
        ->expectsConfirmation(LAYOUT_QUESTION, 'no')
        ->assertSuccessful();
});

it('leaves Alpine to Livewire when Livewire is installed', function () {
    writeCss("@import 'tailwindcss';\n");
    writeJs("import './bootstrap';\n");
    File::put(base_path('composer.json'), json_encode(['require' => ['livewire/livewire' => '^4.0']]));

    $this->artisan('gui:install')
        ->expectsOutputToContain('INCLUDED WITH LIVEWIRE')
        ->expectsConfirmation(LAYOUT_QUESTION, 'no')
        ->assertSuccessful();

    expect(File::get(base_path('resources/js/app.js')))->toBe("import './bootstrap';\n");
});

it('explains how to add Alpine when there is no app.js', function () {
    writeCss("@import 'tailwindcss';\n");

    $this->artisan('gui:install')
        ->expectsConfirmation(LIVEWIRE_QUESTION, 'no')
        ->expectsOutputToContain('npm install alpinejs')
        ->expectsConfirmation(LAYOUT_QUESTION, 'no')
        ->assertSuccessful();
});

it('publishes a layout that shows toasts', function () {
    expect(File::get(__DIR__.'/../../stubs/layout.blade.php'))->toContain('<x-gui::toasts />');
});
