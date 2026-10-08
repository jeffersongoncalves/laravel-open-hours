## Laravel Open Hours

Business opening hours (weekly schedule, exceptions, timezone) stored with spatie/laravel-settings and evaluated by spatie/opening-hours.

### Installation

@verbatim
<code-snippet name="Install" lang="bash">
composer require jeffersongoncalves/laravel-open-hours
php artisan migrate
</code-snippet>
@endverbatim

### Usage

@verbatim
<code-snippet name="Query the schedule" lang="php">
use JeffersonGoncalves\OpenHours\OpenHours;

app(OpenHours::class)->isOpen();
app(OpenHours::class)->nextOpen(); // ?CarbonImmutable
app(OpenHours::class)->statusText(); // "Open now · closes at 18:00"
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Blade" lang="blade">
@openhours ... @else ... @endopenhours
<x-open-hours::status class="badge" />
</code-snippet>
@endverbatim

### Rules
- Edit the schedule through `JeffersonGoncalves\OpenHours\Settings\OpenHoursSettings` (`timezone`, `week`, `exceptions`), not config.
- Ranges are `HH:MM-HH:MM`; exception dates `Y-m-d` (one-off) or `m-d` (every year); an exception with no hours means closed.
- Validate input with `OpenHours::definition($week, $exceptions)` — it throws on invalid ranges.
