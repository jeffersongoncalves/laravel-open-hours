<?php

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use JeffersonGoncalves\OpenHours\OpenHours;
use JeffersonGoncalves\OpenHours\Settings\OpenHoursSettings;
use Spatie\OpeningHours\Exceptions\Exception as OpeningHoursException;

// 2026-10-07 is a Wednesday.
function at(string $datetime): CarbonImmutable
{
    return CarbonImmutable::parse($datetime, 'America/Sao_Paulo');
}

it('seeds the schedule from the config defaults', function () {
    $settings = app(OpenHoursSettings::class);

    expect($settings->week['monday'])->toBe(['09:00-18:00'])
        ->and($settings->week['sunday'])->toBe([])
        ->and($settings->exceptions)->toBe([])
        ->and(app(OpenHours::class)->timezone())->toBe('America/Sao_Paulo');
});

it('tells whether it is open and when that changes', function () {
    $hours = app(OpenHours::class);

    expect($hours->isOpen(at('2026-10-07 10:00')))->toBeTrue()
        ->and($hours->isOpen(at('2026-10-07 19:00')))->toBeFalse()
        ->and($hours->nextClose(at('2026-10-07 10:00'))?->format('Y-m-d H:i'))->toBe('2026-10-07 18:00')
        ->and($hours->nextOpen(at('2026-10-09 19:00'))?->format('Y-m-d H:i'))->toBe('2026-10-12 09:00');
});

it('applies exceptions, recurring ones included', function () {
    $settings = app(OpenHoursSettings::class);
    $settings->exceptions = [
        ['date' => '2026-10-07', 'hours' => [], 'note' => 'Inventory'],
        ['date' => '10-08', 'hours' => ['10:00-12:00']],
    ];
    $settings->save();

    $hours = app(OpenHours::class);

    expect($hours->isOpen(at('2026-10-07 10:00')))->toBeFalse()
        ->and($hours->schedule()->forDate(at('2026-10-07'))->getData())->toBe('Inventory')
        ->and($hours->isOpen(at('2026-10-08 11:00')))->toBeTrue()
        ->and($hours->isOpen(at('2026-10-08 15:00')))->toBeFalse()
        ->and($hours->isOpen(at('2027-10-08 11:00')))->toBeTrue();
});

it('uses the stored timezone', function () {
    $settings = app(OpenHoursSettings::class);
    $settings->timezone = 'Europe/Lisbon';
    $settings->save();

    // 10:00 in São Paulo is 14:00 in Lisbon: still open; 15:00 in São Paulo is 19:00 in Lisbon: closed.
    expect(app(OpenHours::class)->isOpen(at('2026-10-07 10:00')))->toBeTrue()
        ->and(app(OpenHours::class)->isOpen(at('2026-10-07 15:00')))->toBeFalse();
});

it('describes the status in the app locale', function () {
    $hours = app(OpenHours::class);

    expect($hours->statusText(at('2026-10-07 10:00')))->toBe('Open now · closes at 18:00')
        ->and($hours->statusText(at('2026-10-07 07:00')))->toBe('Closed · opens at 09:00')
        ->and($hours->statusText(at('2026-10-09 19:00')))->toBe('Closed · opens Monday at 09:00');

    app()->setLocale('pt_BR');

    expect($hours->statusText(at('2026-10-09 19:00')))->toBe('Fechado · abre segunda-feira às 09:00');
});

it('handles schedules that never open or never close', function () {
    $settings = app(OpenHoursSettings::class);
    $settings->week = array_fill_keys(OpenHours::DAYS, []);
    $settings->save();

    expect(app(OpenHours::class)->statusText(at('2026-10-07 10:00')))->toBe('Closed')
        ->and(app(OpenHours::class)->nextOpen())->toBeNull();

    $settings->week = array_fill_keys(OpenHours::DAYS, ['00:00-24:00']);
    $settings->save();

    expect(app(OpenHours::class)->statusText(at('2026-10-07 10:00')))->toBe('Open now');
});

it('rejects invalid ranges', function () {
    OpenHours::definition(['monday' => ['9h-18h']], []);
})->throws(OpeningHoursException::class);

it('falls back to the config while the settings group is not migrated', function () {
    DB::table('settings')->delete();
    app()->forgetInstance(OpenHoursSettings::class);

    expect(app(OpenHours::class)->isOpen(at('2026-10-07 10:00')))->toBeTrue();
});

it('falls back to the config while the settings table is not migrated', function () {
    Schema::drop('settings');
    app()->forgetInstance(OpenHoursSettings::class);

    expect(app(OpenHours::class)->isOpen(at('2026-10-07 10:00')))->toBeTrue();
});

it('ships a Blade directive and a status component', function () {
    $this->travelTo(at('2026-10-07 10:00'));

    expect(Blade::render('@openhours yes @else no @endopenhours'))->toContain('yes')
        ->and(Blade::render('<x-open-hours::status class="badge" />'))
        ->toContain('class="open-hours-status open-hours-status--open badge"')
        ->toContain('Open now · closes at 18:00');
});
