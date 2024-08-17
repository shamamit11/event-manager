<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Domains\Calendar\Repositories\EventRepositoryInterface;
use App\Application\UseCases\DeleteEventUseCase;
use App\Domains\Calendar\Exceptions\EventNotFoundException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->eventRepository = Mockery::mock(EventRepositoryInterface::class);
});

test('deletes an event successfully', function () {
    $eventId = 1;

    $this->eventRepository
        ->shouldReceive('deleteById')
        ->with($eventId)
        ->once();

    $deleteEventUseCase = new DeleteEventUseCase($this->eventRepository);

    $deleteEventUseCase->execute($eventId);

    $this->eventRepository->shouldHaveReceived('deleteById')->with($eventId);
});

test('handles non-existent event ID gracefully', function () {
    $eventId = 999;

    $this->eventRepository
        ->shouldReceive('deleteById')
        ->with($eventId)
        ->andThrow(new EventNotFoundException());

    $deleteEventUseCase = new DeleteEventUseCase($this->eventRepository);

    expect(function () use ($deleteEventUseCase, $eventId) {
        $deleteEventUseCase->execute($eventId);
    })->toThrow(EventNotFoundException::class);
});
