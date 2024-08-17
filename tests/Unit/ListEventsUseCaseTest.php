<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Domains\Calendar\Repositories\EventRepositoryInterface;
use App\Application\UseCases\ListEventsUseCase;
use Illuminate\Pagination\LengthAwarePaginator;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->eventRepository = Mockery::mock(EventRepositoryInterface::class);
});

test('lists events successfully', function () {
    $start = '2024-08-01T00:00:00';
    $end = '2024-08-31T23:59:59';
    $perPage = 10;
    $page = 1;

    $paginator = Mockery::mock(LengthAwarePaginator::class);
    $paginator->shouldReceive('count')->andReturn(5);

    $this->eventRepository
        ->shouldReceive('findByDateRange')
        ->with($start, $end, $perPage, $page)
        ->andReturn($paginator);

    $listEventsUseCase = new ListEventsUseCase($this->eventRepository);

    $result = $listEventsUseCase->execute($start, $end, $perPage, $page);

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class);
    expect($result->count())->toBe(5);
    $this->eventRepository->shouldHaveReceived('findByDateRange');
});

test('handles pagination correctly', function () {
    $start = '2024-08-01T00:00:00';
    $end = '2024-08-31T23:59:59';
    $perPage = 10;
    $page = 2;

    $paginator = Mockery::mock(LengthAwarePaginator::class);
    $paginator->shouldReceive('count')->andReturn(15);

    $this->eventRepository
        ->shouldReceive('findByDateRange')
        ->with($start, $end, $perPage, $page)
        ->andReturn($paginator);

    $listEventsUseCase = new ListEventsUseCase($this->eventRepository);

    $result = $listEventsUseCase->execute($start, $end, $perPage, $page);

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class);
    expect($result->count())->toBe(15);
    $this->eventRepository->shouldHaveReceived('findByDateRange');
});
