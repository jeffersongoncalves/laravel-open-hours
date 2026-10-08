<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    |
    | Seed the database settings on `php artisan migrate`, and are used as-is
    | while the settings can't be read (migrations pending). After that, the
    | schedule is edited in the database (or the Filament plugin).
    |
    | Ranges are "HH:MM-HH:MM"; overnight ranges ("22:00-02:00") are allowed.
    | Exception dates are "Y-m-d", or "m-d" to repeat every year.
    |
    */
    'timezone' => null, // null = app.timezone

    'week' => [
        'monday' => ['09:00-18:00'],
        'tuesday' => ['09:00-18:00'],
        'wednesday' => ['09:00-18:00'],
        'thursday' => ['09:00-18:00'],
        'friday' => ['09:00-18:00'],
        'saturday' => [],
        'sunday' => [],
    ],

    'exceptions' => [
        // ['date' => '12-25', 'hours' => [], 'note' => 'Christmas'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Time format
    |--------------------------------------------------------------------------
    |
    | Used by the status text (`<x-open-hours::status />`).
    |
    */
    'time_format' => 'H:i',
];
