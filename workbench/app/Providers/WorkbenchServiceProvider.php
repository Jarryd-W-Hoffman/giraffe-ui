<?php

namespace Workbench\App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Workbench\App\Console\GenerateDocs;
use Workbench\App\Livewire\ProfileForm;
use Workbench\App\Livewire\SettingsTabs;
use Workbench\App\Livewire\UsersTable;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the catalog.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'catalog');
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');

        // Livewire is a dev dependency, used to prove the components work with it.
        if (class_exists(Livewire::class)) {
            Livewire::component('profile-form', ProfileForm::class);
            Livewire::component('users-table', UsersTable::class);
            Livewire::component('settings-tabs', SettingsTabs::class);

            // Livewire bundles Alpine, so load its script on every catalog page instead of a second copy of Alpine.
            Livewire::forceAssetInjection();
        }

        if ($this->app->runningInConsole()) {
            $this->commands([GenerateDocs::class]);
        }
    }
}
