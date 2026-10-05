<?php

namespace JayAitch\GiraffeUI\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use JayAitch\GiraffeUI\GiraffeUIServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        // Livewire and blade-icons are optional. CI also runs the tests without them (minus the groups that need them).
        return array_values(array_filter([
            class_exists(LivewireServiceProvider::class) ? LivewireServiceProvider::class : null,
            class_exists(BladeIconsServiceProvider::class) ? BladeIconsServiceProvider::class : null,
            class_exists(BladeHeroiconsServiceProvider::class) ? BladeHeroiconsServiceProvider::class : null,
            GiraffeUIServiceProvider::class,
        ]));
    }

    protected function defineEnvironment($app): void
    {
        // The catalog routes use the web middleware, which encrypts cookies.
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));
    }
}
