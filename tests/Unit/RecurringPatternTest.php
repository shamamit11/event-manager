<?php

use App\Domains\Calendar\ValueObjects\RecurringPattern;
use DateTime;
use InvalidArgumentException;

test('RecurringPattern constructor assigns values correctly for valid frequency', function () {
    $frequency = 'daily';
    $repeatUntil = new DateTime('2025-08-20T10:00:00');

    $recurringPattern = new RecurringPattern($frequency, $repeatUntil);

    expect($recurringPattern->getFrequency())->toBe($frequency);
    expect($recurringPattern->getRepeatUntil())->toBe($repeatUntil);
});

test('RecurringPattern throws exception for invalid frequency value', function () {
    $invalidFrequency = 'invalid_frequency';
    $repeatUntil = new DateTime('2025-08-20T10:00:00');

    expect(function () use ($invalidFrequency, $repeatUntil) {
        new RecurringPattern($invalidFrequency, $repeatUntil);
    })->toThrow(InvalidArgumentException::class);
});

test('RecurringPattern getFrequency returns correct value', function () {
    $frequency = 'weekly';
    $repeatUntil = new DateTime('2025-08-20T10:00:00');

    $recurringPattern = new RecurringPattern($frequency, $repeatUntil);

    expect($recurringPattern->getFrequency())->toBe($frequency);
});

test('RecurringPattern getRepeatUntil returns correct DateTime', function () {
    $frequency = 'monthly';
    $repeatUntil = new DateTime('2025-08-20T10:00:00');

    $recurringPattern = new RecurringPattern($frequency, $repeatUntil);

    expect($recurringPattern->getRepeatUntil())->toBe($repeatUntil);
});
