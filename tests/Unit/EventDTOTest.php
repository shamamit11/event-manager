<?php

use App\Application\DTOs\EventDTO;

test('EventDTO constructor assigns values correctly', function () {
    $title = 'Sample Event';
    $description = 'This is a sample event description.';
    $start = '2024-08-20T10:00:00';
    $end = '2024-08-30T10:00:00';
    $recurringPattern = true;
    $frequency = 'daily';
    $repeat_until = '2025-08-20T10:00:00';

    $eventDTO = new EventDTO(
        $title,
        $description,
        $start,
        $end,
        $recurringPattern,
        $frequency,
        $repeat_until
    );

    expect($eventDTO->title)->toBe($title);
    expect($eventDTO->description)->toBe($description);
    expect($eventDTO->start)->toBe($start);
    expect($eventDTO->end)->toBe($end);
    expect($eventDTO->recurringPattern)->toBe($recurringPattern);
    expect($eventDTO->frequency)->toBe($frequency);
    expect($eventDTO->repeat_until)->toBe($repeat_until);
});
