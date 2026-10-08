@php($openHours = app(\JeffersonGoncalves\OpenHours\OpenHours::class))
<span {{ $attributes->class(['open-hours-status', $openHours->isOpen() ? 'open-hours-status--open' : 'open-hours-status--closed']) }}>{{ $openHours->statusText() }}</span>
