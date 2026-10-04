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
}
