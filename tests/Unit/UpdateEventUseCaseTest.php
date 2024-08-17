<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Domains\Calendar\Repositories\EventRepositoryInterface;
use App\Domains\Calendar\ValueObjects\EventDateRange;
use App\Domains\Calendar\ValueObjects\RecurringPattern;
use App\Domains\Calendar\Entities\Event;
use App\Application\UseCases\UpdateEventUseCase;
use App\Domains\Calendar\Exceptions\OverlappingEventException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->eventRepository = Mockery::mock(EventRepositoryInterface::class);
});

test('updates an event successfully', function () {

    $existingEvent = new Event(
        1,
        'Original Title',
        'Original Description',
        new EventDateRange(new DateTime('2024-08-20T10:00:00'), new DateTime('2024-08-30T10:00:00')),
        new RecurringPattern('daily', new DateTime('2025-08-20T10:00:00'))
    );

    $updatedEvent = new Event(
        1,
        'Updated Title',
        'Updated Description',
        new EventDateRange(new DateTime('2024-09-01T10:00:00'), new DateTime('2024-09-15T10:00:00')),
        new RecurringPattern('weekly', new DateTime('2026-09-01T10:00:00'))
    );

    $this->eventRepository
        ->shouldReceive('findById')
        ->with(1)
        ->andReturn($existingEvent);

    $this->eventRepository
        ->shouldReceive('findOverlapping')
        ->with(Mockery::type(EventDateRange::class))
        ->andReturn([]);

    $this->eventRepository
        ->shouldReceive('save')
        ->with(Mockery::on(function ($arg) use ($updatedEvent) {
            return $arg->id === $updatedEvent->id &&
                $arg->title === $updatedEvent->title &&
                $arg->description === $updatedEvent->description &&
                $arg->dateRange->getStart() == $updatedEvent->dateRange->getStart() &&
                $arg->dateRange->getEnd() == $updatedEvent->dateRange->getEnd() &&
                $arg->recurringPattern->frequency === $updatedEvent->recurringPattern->frequency &&
                $arg->recurringPattern->repeat_until == $updatedEvent->recurringPattern->repeat_until;
        }))
        ->andReturn($updatedEvent);

    $updateEventUseCase = new UpdateEventUseCase($this->eventRepository);

    $updateEventUseCase->execute(
        1,
        'Updated Title',
        'Updated Description',
        '2024-09-01T10:00:00',
        '2024-09-15T10:00:00',
        true,
        'weekly',
        '2026-09-01T10:00:00'
    );

    $this->eventRepository->shouldHaveReceived('save');
});

test('throws an exception if the event to be updated does not exist', function () {
    $this->eventRepository
        ->shouldReceive('findById')
        ->with(1)
        ->andReturn(null);

    $updateEventUseCase = new UpdateEventUseCase($this->eventRepository);

    expect(function () use ($updateEventUseCase) {
        $updateEventUseCase->execute(
            1,
            'Updated Title',
            'Updated Description',
            '2024-09-01T10:00:00',
            '2024-09-15T10:00:00',
            true,
            'weekly',
            '2026-09-01T10:00:00'
        );
    })->toThrow(\InvalidArgumentException::class);
});

test('throws an exception if there are overlapping events', function () {
    $existingEvent = new Event(
        1,
        'Original Title',
        'Original Description',
        new EventDateRange(new DateTime('2024-08-20T10:00:00'), new DateTime('2024-08-30T10:00:00')),
        new RecurringPattern('daily', new DateTime('2025-08-20T10:00:00'))
    );

    $overlappingEvent = new Event(
        2,
        'Overlapping Event',
        'Description',
        new EventDateRange(new DateTime('2024-09-01T10:00:00'), new DateTime('2024-09-15T10:00:00')),
        null
    );

    $this->eventRepository
        ->shouldReceive('findById')
        ->with(1)
        ->andReturn($existingEvent);

    $this->eventRepository
        ->shouldReceive('findOverlapping')
        ->with(Mockery::type(EventDateRange::class))
        ->andReturn([$overlappingEvent]);

    $updateEventUseCase = new UpdateEventUseCase($this->eventRepository);

    expect(function () use ($updateEventUseCase) {
        $updateEventUseCase->execute(
            1,
            'Updated Title',
            'Updated Description',
            '2024-09-01T10:00:00',
            '2024-09-15T10:00:00',
            false,
            null,
            null
        );
    })->toThrow(OverlappingEventException::class);
});
