<?php

namespace JeffersonGoncalves\OpenHours;

use Carbon\CarbonImmutable;
use DateTimeInterface;
use Illuminate\Database\QueryException;
use JeffersonGoncalves\OpenHours\Settings\OpenHoursSettings;
use Spatie\LaravelSettings\Exceptions\MissingSettings;
use Spatie\OpeningHours\OpeningHours as Schedule;
use Throwable;

class OpenHours
{
    public const DAYS = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    /**
     * The stored schedule as a spatie/opening-hours instance; the config defaults while the settings can't be read.
     */
    public function schedule(): Schedule
    {
        [$timezone, $week, $exceptions] = $this->stored();

        return Schedule::create(self::definition($week, $exceptions), $timezone);
    }

    public function timezone(): string
    {
        return $this->stored()[0];
    }

    public function isOpen(?DateTimeInterface $at = null): bool
    {
        return $this->schedule()->isOpenAt($this->moment($at));
    }

    public function nextOpen(?DateTimeInterface $at = null): ?CarbonImmutable
    {
        try {
            return CarbonImmutable::instance($this->schedule()->nextOpen($this->moment($at)))->setTimezone($this->timezone());
        } catch (Throwable) {
            return null; // never opens (empty schedule)
        }
    }

    public function nextClose(?DateTimeInterface $at = null): ?CarbonImmutable
    {
        try {
            return CarbonImmutable::instance($this->schedule()->nextClose($this->moment($at)))->setTimezone($this->timezone());
        } catch (Throwable) {
            return null; // never closes (open 24/7)
        }
    }

    /** "Open now · closes at 18:00", "Closed · opens Monday at 09:00"... in the app locale. */
    public function statusText(?DateTimeInterface $at = null): string
    {
        $now = $this->moment($at);
        $open = $this->isOpen($now);
        $next = $open ? $this->nextClose($now) : $this->nextOpen($now);
        $state = $open ? 'open' : 'closed';

        if ($next === null) {
            return __("open-hours::status.{$state}");
        }

        $time = $next->format((string) config('open-hours.time_format', 'H:i'));

        if ($next->isSameDay($now)) {
            return __("open-hours::status.{$state}_today", ['time' => $time]);
        }

        $day = $next->locale(app()->getLocale())->translatedFormat('l');

        return __("open-hours::status.{$state}_on", ['day' => $day, 'time' => $time]);
    }

    /**
     * spatie/opening-hours data from the stored shape. Throws on an invalid range, so it doubles as validation.
     *
     * @param  array<string, list<string>>  $week
     * @param  list<array{date: string, hours: list<string>, note?: string|null}>  $exceptions
     * @return array<string, mixed>
     */
    public static function definition(array $week, array $exceptions): array
    {
        $data = [];
        foreach (self::DAYS as $day) {
            $data[$day] = array_values(array_filter($week[$day] ?? [], 'filled'));
        }

        foreach ($exceptions as $exception) {
            $hours = array_values(array_filter($exception['hours'], 'filled'));
            $data['exceptions'][$exception['date']] = filled($exception['note'] ?? null) ? [...$hours, 'data' => $exception['note']] : $hours;
        }

        Schedule::create($data); // validate

        return $data;
    }

    private function moment(?DateTimeInterface $at): CarbonImmutable
    {
        return $at !== null ? CarbonImmutable::instance($at)->setTimezone($this->timezone()) : CarbonImmutable::now($this->timezone());
    }

    /**
     * @return array{string, array<string, list<string>>, list<array{date: string, hours: list<string>, note?: string|null}>}
     */
    private function stored(): array
    {
        try {
            $settings = app(OpenHoursSettings::class);
            [$timezone, $week, $exceptions] = [$settings->timezone, $settings->week, $settings->exceptions];
        } catch (QueryException|MissingSettings) {
            // settings table or group not migrated yet
            [$timezone, $week, $exceptions] = [(string) config('open-hours.timezone'), (array) config('open-hours.week', []), array_values((array) config('open-hours.exceptions', []))];
        }

        return [$timezone !== '' ? $timezone : (string) config('app.timezone', 'UTC'), $week, $exceptions];
    }
}
