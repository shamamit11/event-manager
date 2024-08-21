<?php

use App\Application\Services\OccurrenceGenerator;
use App\Domains\Calendar\ValueObjects\EventDateRange;
use App\Domains\Calendar\ValueObjects\RecurringPattern;
use DateTime;

test('generates daily occurrences correctly', function () {
    $generator = new OccurrenceGenerator();
    $start = new DateTime('2024-08-20T10:00:00');
    $end = new DateTime('2024-08-20T11:00:00');
    $dateRange = new EventDateRange($start, $end);
    $repeatUntil = new DateTime('2024-08-22T10:00:00');
    $recurringPattern = new RecurringPattern('daily', $repeatUntil);

    $occurrences = $generator->generateOccurrences('Test Event', 'Event Description', $dateRange, $recurringPattern);

    expect($occurrences)->toHaveCount(3);

    expect($occurrences[0]->title)->toBe('Test Event');
    expect($occurrences[0]->dateRange->getStart()->format('Y-m-d'))->toBe('2024-08-20');
    expect($occurrences[1]->dateRange->getStart()->format('Y-m-d'))->toBe('2024-08-21');
    expect($occurrences[2]->dateRange->getStart()->format('Y-m-d'))->toBe('2024-08-22');
});
