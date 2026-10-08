<?php

namespace JeffersonGoncalves\OpenHours;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class OpenHoursServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-open-hours')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations();
    }
}
