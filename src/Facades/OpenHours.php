<?php

namespace JeffersonGoncalves\OpenHours\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JeffersonGoncalves\OpenHours\OpenHours
 */
class OpenHours extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-open-hours';
    }
}
