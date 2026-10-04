<?php

namespace Workbench\App\Providers;

use Illuminate\Support\ServiceProvider;
use Workbench\App\Console\GenerateDocs;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the catalog.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'catalog');
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->commands([GenerateDocs::class]);
        }
    }
}
