<?php

namespace JeffersonGoncalves\OpenHours\Tests;

use JeffersonGoncalves\OpenHours\OpenHoursServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            OpenHoursServiceProvider::class,
        ];
    }
}
