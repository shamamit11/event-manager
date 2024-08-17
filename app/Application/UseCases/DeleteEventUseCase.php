<?php

namespace App\Application\UseCases;

use App\Domains\Calendar\Repositories\EventRepositoryInterface;

class DeleteEventUseCase
{
    private EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function execute(int $id): void
    {
        $this->eventRepository->deleteById($id);
    }
}
