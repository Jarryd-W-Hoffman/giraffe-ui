<?php

namespace JayAitch\GiraffeUI\Tests;

use JayAitch\GiraffeUI\GiraffeUIServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        // CI also runs the tests without Livewire installed (minus the livewire group), to prove it's optional.
        return class_exists(LivewireServiceProvider::class)
            ? [LivewireServiceProvider::class, GiraffeUIServiceProvider::class]
            : [GiraffeUIServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        // The catalog routes use the web middleware, which encrypts cookies.
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));
    }
}
