<?php

namespace JeffersonGoncalves\OpenHours\Settings;

use Spatie\LaravelSettings\Settings;

class OpenHoursSettings extends Settings
{
    /** Empty = app.timezone. */
    public string $timezone;

    /**
     * monday..sunday => ["09:00-12:00", "13:00-18:00"]. (@phpstan-var: laravel-settings derives casts from @var.)
     *
     * @phpstan-var array<string, list<string>>
     */
    public array $week;

    /**
     * [["date" => "2026-12-25" (or "12-25" every year), "hours" => [] (closed), "note" => "Christmas"], ...]
     *
     * @phpstan-var list<array{date: string, hours: list<string>, note?: string|null}>
     */
    public array $exceptions;

    public static function group(): string
    {
        return 'open-hours';
    }
}
