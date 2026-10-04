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
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'giraffeui');

        // Most components are anonymous and live in resources/views/components.
        // The few that need PHP (e.g. Field) are classes in src/Components.
        // Usage: <x-gui::button />
        Blade::componentNamespace('JayAitch\\GiraffeUI\\Components', 'gui');
        Blade::anonymousComponentPath(__DIR__.'/../resources/views/components', 'gui');

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }
}
