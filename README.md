<div class="filament-hidden">

![Laravel Open Hours](https://raw.githubusercontent.com/jeffersongoncalves/laravel-open-hours/main/art/jeffersongoncalves-laravel-open-hours.png)

</div>

# Laravel Open Hours

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jeffersongoncalves/laravel-open-hours.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-open-hours)
[![Tests](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/laravel-open-hours/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/jeffersongoncalves/laravel-open-hours/actions?query=workflow%3ATests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jeffersongoncalves/laravel-open-hours.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-open-hours)

Your business opening hours, editable at runtime: a weekly schedule, holidays and special dates (one-off or every year) and a timezone, stored with [spatie/laravel-settings](https://github.com/spatie/laravel-settings) and evaluated by [spatie/opening-hours](https://github.com/spatie/opening-hours). Ask whether you're open, when you open or close next, or drop a ready-made "Open now · closes at 18:00" badge into any Blade view.

For an admin UI, use [jeffersongoncalves/filament-open-hours](https://github.com/jeffersongoncalves/filament-open-hours).

## Installation

```bash
composer require jeffersongoncalves/laravel-open-hours
php artisan migrate
```

The settings migration seeds the schedule from `config/open-hours.php` (Monday–Friday 09:00–18:00 by default). Publish the config first if you want other defaults:

```bash
php artisan vendor:publish --tag=open-hours-config
```

## Usage

```php
use JeffersonGoncalves\OpenHours\OpenHours;

$hours = app(OpenHours::class);

$hours->isOpen();               // now, in the schedule's timezone
$hours->isOpen($dateTime);
$hours->nextOpen();             // CarbonImmutable|null (null = never opens)
$hours->nextClose();            // CarbonImmutable|null (null = open 24/7)
$hours->statusText();           // "Open now · closes at 18:00", "Closed · opens Monday at 09:00"
$hours->schedule();             // the full Spatie\OpeningHours\OpeningHours instance
```

### Blade

```blade
@openhours
    <a href="https://wa.me/5511999999999">Chat with us</a>
@else
    <p>We're closed right now — leave a message.</p>
@endopenhours

<x-open-hours::status class="badge" />
```

The component renders a `<span class="open-hours-status open-hours-status--open|--closed">` with the status text in the app locale (19 languages included).

### Editing the schedule

```php
use JeffersonGoncalves\OpenHours\Settings\OpenHoursSettings;

$settings = app(OpenHoursSettings::class);
$settings->timezone = 'America/Sao_Paulo'; // '' = app.timezone
$settings->week['saturday'] = ['09:00-13:00'];
$settings->exceptions = [
    ['date' => '12-25', 'hours' => [], 'note' => 'Christmas'],         // every year, closed
    ['date' => '2026-12-24', 'hours' => ['09:00-12:00'], 'note' => null], // one-off, short day
];
$settings->save();
```

Ranges are `HH:MM-HH:MM`, several per day, overnight ranges (`22:00-02:00`) allowed. `OpenHours::definition($week, $exceptions)` throws on an invalid range, so you can use it to validate input.

If the settings can't be read yet (migrations pending) the config defaults are used.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Jefferson Gonçalves](https://github.com/jeffersongoncalves)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
