<?php

namespace JayAitch\GiraffeUI\Tests;

use JayAitch\GiraffeUI\GiraffeUIServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [GiraffeUIServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        // The catalog routes use the web middleware, which encrypts cookies.
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));
    }
}
