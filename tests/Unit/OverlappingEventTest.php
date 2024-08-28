<?php

use App\Domains\Calendar\ValueObjects\EventDateRange;
use App\Infrastructure\Persistence\Eloquent\Event as EloquentEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Domains\Calendar\Repositories\EventRepositoryInterface;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->repository = Mockery::mock(EventRepositoryInterface::class);
});

test('detects event starting before and ending after the range', function () {
    $this->repository->shouldReceive('findOverlapping')
        ->once()
        ->andReturn([
            (object) ['id' => 1]
        ]);

    $searchRange = new EventDateRange(
        new \DateTime('2024-08-10 10:00:00'),
        new \DateTime('2024-08-20 10:00:00')
    );

    $overlappingEvents = $this->repository->findOverlapping($searchRange);

    expect($overlappingEvents)->toHaveCount(1);
    expect($overlappingEvents[0])->toHaveKey('id', 1);
});

test('detects event starting and ending within the range', function () {
    $this->repository->shouldReceive('findOverlapping')
        ->once()
        ->andReturn([
            (object) ['id' => 1]
        ]);

    $searchRange = new EventDateRange(
        new \DateTime('2024-08-10 10:00:00'),
        new \DateTime('2024-08-20 10:00:00')
    );

    $overlappingEvents = $this->repository->findOverlapping($searchRange);

    expect($overlappingEvents)->toHaveCount(1);
    expect($overlappingEvents[0])->toHaveKey('id', 1);
});

test('detects event starting before and ending within the range', function () {
    $this->repository->shouldReceive('findOverlapping')
        ->once()
        ->andReturn([
            (object) ['id' => 1]
        ]);

    $searchRange = new EventDateRange(
        new \DateTime('2024-08-10 10:00:00'),
        new \DateTime('2024-08-20 10:00:00')
    );

    $overlappingEvents = $this->repository->findOverlapping($searchRange);

    expect($overlappingEvents)->toHaveCount(1);
    expect($overlappingEvents[0])->toHaveKey('id', 1);
});

test('detects event starting within and ending after the range', function () {
    $this->repository->shouldReceive('findOverlapping')
        ->once()
        ->andReturn([
            (object) ['id' => 1]
        ]);

    $searchRange = new EventDateRange(
        new \DateTime('2024-08-10 10:00:00'),
        new \DateTime('2024-08-20 10:00:00')
    );

    $overlappingEvents = $this->repository->findOverlapping($searchRange);

    expect($overlappingEvents)->toHaveCount(1);
    expect($overlappingEvents[0])->toHaveKey('id', 1);
});
