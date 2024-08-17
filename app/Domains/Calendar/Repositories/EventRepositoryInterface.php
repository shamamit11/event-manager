<?php

namespace App\Domains\Calendar\Repositories;

use App\Domains\Calendar\Entities\Event;
use App\Domains\Calendar\ValueObjects\EventDateRange;
use Illuminate\Pagination\LengthAwarePaginator;

interface EventRepositoryInterface
{
    public function save(Event $event): Event;
    public function findOverlapping(EventDateRange $dateRange): array;
    public function findById(int $id): ?Event;
    public function findByDateRange(string $start, string $end, int $perPage = 15, int $page = 1): LengthAwarePaginator;
    public function deleteById(int $id): void;
}
