<?php

namespace JeffersonGoncalves\OpenHours;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use JeffersonGoncalves\OpenHours\Settings\OpenHoursSettings;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class OpenHoursServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-open-hours')
            ->hasConfigFile('open-hours')
            ->hasViews('open-hours')
            ->hasTranslations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(OpenHours::class);

        Config::set('settings.settings', array_merge(Config::get('settings.settings', []), [OpenHoursSettings::class]));
    }

    public function packageBooted(): void
    {
        $migrations = __DIR__.'/../database/settings';

        Config::set('settings.migrations_paths', array_merge([$migrations], Config::get('settings.migrations_paths', [])));
        $this->publishes([$migrations => database_path('settings')], 'open-hours-settings-migrations');

        Blade::if('openhours', fn () => $this->app->make(OpenHours::class)->isOpen());
    }
}
