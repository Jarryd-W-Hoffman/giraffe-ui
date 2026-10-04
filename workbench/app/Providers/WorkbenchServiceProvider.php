<?php

namespace Workbench\App\Providers;

use Illuminate\Support\ServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the catalog.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'catalog');
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
    }
}
