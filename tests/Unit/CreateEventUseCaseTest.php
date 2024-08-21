<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Domains\Calendar\Repositories\EventRepositoryInterface;
use App\Domains\Calendar\ValueObjects\EventDateRange;
use App\Domains\Calendar\ValueObjects\RecurringPattern;
use App\Domains\Calendar\Entities\Event;
use App\Application\UseCases\CreateEventUseCase;
use App\Application\Services\OccurrenceGenerator;
use App\Domains\Calendar\Exceptions\OverlappingEventException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->eventRepository = Mockery::mock(EventRepositoryInterface::class);
    $this->occurrenceGenerator = Mockery::mock(OccurrenceGenerator::class);
});

test('creates an event successfully', function () {

    $eventDateRange = new EventDateRange(new DateTime('2024-08-20T10:00:00'), new DateTime('2024-08-30T10:00:00'));
    $recurringPattern = new RecurringPattern('daily', new DateTime('2025-08-20T10:00:00'));

    $event = new Event(
        null,
        'Test Event',
        'Event Description',
        $eventDateRange,
        $recurringPattern
    );

    $this->eventRepository
        ->shouldReceive('findOverlapping')
        ->with(Mockery::type(EventDateRange::class))
        ->andReturn([]);

    $this->eventRepository
        ->shouldReceive('save')
        ->with(Mockery::on(function ($arg) use ($event) {
            return $arg->title === $event->title &&
                $arg->description === $event->description &&
                $arg->dateRange->getStart() == $event->dateRange->getStart() &&
                $arg->dateRange->getEnd() == $event->dateRange->getEnd() &&
                $arg->recurringPattern->frequency === $event->recurringPattern->frequency &&
                $arg->recurringPattern->repeat_until == $event->recurringPattern->repeat_until;
        }))
        ->andReturn($event);

    $this->occurrenceGenerator
        ->shouldReceive('generateOccurrences')
        ->with('Test Event', 'Event Description', Mockery::type(EventDateRange::class), Mockery::type(RecurringPattern::class))
        ->andReturn([]);

    $createEventUseCase = new CreateEventUseCase($this->eventRepository, $this->occurrenceGenerator);

    $result = $createEventUseCase->execute(
        'Test Event',
        'Event Description',
        '2024-08-20T10:00:00',
        '2024-08-30T10:00:00',
        true,
        'daily',
        '2025-08-20T10:00:00'
    );

    expect($result)->toBeInstanceOf(Event::class);
    expect($result->title)->toBe('Test Event');
    expect($result->description)->toBe('Event Description');
    expect($result->dateRange->getStart()->format('Y-m-d\TH:i:s'))->toBe('2024-08-20T10:00:00');
    expect($result->dateRange->getEnd()->format('Y-m-d\TH:i:s'))->toBe('2024-08-30T10:00:00');
    expect($result->recurringPattern->frequency)->toBe('daily');
    expect($result->recurringPattern->repeat_until->format('Y-m-d\TH:i:s'))->toBe('2025-08-20T10:00:00');
});


test('throws an exception if there are overlapping events', function () {

    $eventDateRange = new EventDateRange(new DateTime('2024-08-20T10:00:00'), new DateTime('2024-08-30T10:00:00'));

    $this->eventRepository
        ->shouldReceive('findOverlapping')
        ->with(Mockery::type(EventDateRange::class))
        ->andReturn([new Event(1, 'Overlapping Event', null, $eventDateRange, null)]);

    $createEventUseCase = new CreateEventUseCase($this->eventRepository, $this->occurrenceGenerator);

    expect(function () use ($createEventUseCase) {
        $createEventUseCase->execute(
            'Test Event',
            'Event Description',
            '2024-08-20T10:00:00',
            '2024-08-30T10:00:00',
            false,
            null,
            null
        );
    })->toThrow(OverlappingEventException::class);
});

test('throws an exception if frequency or repeat_until is missing for recurring events', function () {

    $createEventUseCase = new CreateEventUseCase($this->eventRepository, $this->occurrenceGenerator);

    expect(function () use ($createEventUseCase) {
        $createEventUseCase->execute(
            'Test Event',
            'Event Description',
            '2024-08-20T10:00:00',
            '2024-08-30T10:00:00',
            true,
            null,
            null
        );
    })->toThrow(\InvalidArgumentException::class);
});
