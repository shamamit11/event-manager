<?php

namespace App\Application\UseCases;

use App\Domains\Calendar\Repositories\EventRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ListEventsUseCase
{
    private EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function execute(string $start, string $end, int $perPage, int $page): LengthAwarePaginator
    {
        return $this->eventRepository->findByDateRange($start, $end, $perPage, $page);
    }
}
