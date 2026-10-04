<?php

namespace JayAitch\GiraffeUI;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use JayAitch\GiraffeUI\Console\InstallCommand;

class GiraffeUIServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        // Anonymous components live in resources/views/components.
        // Usage: <x-gui::button />
        Blade::anonymousComponentPath(__DIR__.'/../resources/views/components', 'gui');

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }
}
