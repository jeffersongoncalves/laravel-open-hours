<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

/**
 * Seeds the schedule from config/open-hours.php.
 */
return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('open-hours', function (SettingsBlueprint $blueprint): void {
            $blueprint->add('timezone', (string) config('open-hours.timezone'));
            $blueprint->add('week', (array) config('open-hours.week', []));
            $blueprint->add('exceptions', array_values((array) config('open-hours.exceptions', [])));
        });
    }
};
