<?php

use App\Domains\Calendar\ValueObjects\EventDateRange;
use DateTime;
use InvalidArgumentException;

test('EventDateRange constructor assigns values correctly', function () {
    $start = new DateTime('2024-08-20T10:00:00');
    $end = new DateTime('2024-08-30T10:00:00');

    $dateRange = new EventDateRange($start, $end);

    expect($dateRange->getStart())->toBe($start);
    expect($dateRange->getEnd())->toBe($end);
});

test('EventDateRange throws exception if start date is not before end date', function () {
    $start = new DateTime('2024-08-30T10:00:00');
    $end = new DateTime('2024-08-20T10:00:00');

    expect(function () use ($start, $end) {
        new EventDateRange($start, $end);
    })->toThrow(InvalidArgumentException::class);
});

test('EventDateRange overlapsWith detects overlapping date ranges', function () {
    $range1Start = new DateTime('2024-08-20T10:00:00');
    $range1End = new DateTime('2024-08-30T10:00:00');
    $range1 = new EventDateRange($range1Start, $range1End);

    $range2Start = new DateTime('2024-08-25T10:00:00');
    $range2End = new DateTime('2024-09-05T10:00:00');
    $range2 = new EventDateRange($range2Start, $range2End);

    expect($range1->overlapsWith($range2))->toBeTrue();
});

test('EventDateRange overlapsWith detects non-overlapping date ranges', function () {
    $range1Start = new DateTime('2024-08-20T10:00:00');
    $range1End = new DateTime('2024-08-30T10:00:00');
    $range1 = new EventDateRange($range1Start, $range1End);

    $range2Start = new DateTime('2024-09-01T10:00:00');
    $range2End = new DateTime('2024-09-10T10:00:00');
    $range2 = new EventDateRange($range2Start, $range2End);

    expect($range1->overlapsWith($range2))->toBeFalse();
});
