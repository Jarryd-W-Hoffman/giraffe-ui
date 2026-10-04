<?php

namespace Workbench\App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Workbench\App\Console\GenerateDocs;
use Workbench\App\Livewire\AvatarUpload;
use Workbench\App\Livewire\ProfileForm;
use Workbench\App\Livewire\SettingsTabs;
use Workbench\App\Livewire\SignupWizard;
use Workbench\App\Livewire\UsersTable;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * The catalog runs on Testbench's bare app, which has no database tables. Livewire needs a cache and a session
     * that persist between requests, so keep both on disk whatever the generated skeleton .env says.
     */
    public function register(): void
    {
        $this->app['config']->set('cache.default', 'file');
        $this->app['config']->set('session.driver', 'file');
    }

    /**
     * Bootstrap the catalog.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'catalog');
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');

        // Pieces shared by the full-page examples, e.g. <x-example::app-shell>.
        Blade::anonymousComponentPath(__DIR__.'/../../resources/views/examples/components', 'example');

        // Livewire is a dev dependency, used to prove the components work with it.
        if (class_exists(Livewire::class)) {
            Livewire::component('profile-form', ProfileForm::class);
            Livewire::component('users-table', UsersTable::class);
            Livewire::component('settings-tabs', SettingsTabs::class);
            Livewire::component('signup-wizard', SignupWizard::class);
            Livewire::component('avatar-upload', AvatarUpload::class);

            // Livewire bundles Alpine, so load its script on every catalog page instead of a second copy of Alpine.
            Livewire::forceAssetInjection();
        }

        if ($this->app->runningInConsole()) {
            $this->commands([GenerateDocs::class]);
        }
    }
}
