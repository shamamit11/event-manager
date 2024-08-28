<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domains\Calendar\Entities\Event as DomainEvent;
use App\Domains\Calendar\Exceptions\EventNotFoundException;
use App\Domains\Calendar\Repositories\EventRepositoryInterface;
use App\Domains\Calendar\ValueObjects\EventDateRange;
use App\Domains\Calendar\ValueObjects\RecurringPattern;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Infrastructure\Persistence\Eloquent\Event as EloquentEvent;

class EloquentEventRepository implements EventRepositoryInterface
{
    public function save(DomainEvent $event): DomainEvent
    {
        $eloquentEvent = EloquentEvent::updateOrCreate(
            ['id' => $event->getId()],
            [
                'title' => $event->title,
                'description' => $event->description,
                'start' => $event->dateRange->getStart()->format('Y-m-d H:i:s'),
                'end' => $event->dateRange->getEnd()->format('Y-m-d H:i:s'),
                'recurring_pattern' => $event->hasRecurringPattern(),
                'frequency' => $event->hasRecurringPattern() ? $event->recurringPattern->frequency : null,
                'repeat_until' => $event->hasRecurringPattern() ? $event->recurringPattern->repeat_until->format('Y-m-d H:i:s') : null,
                'parent_id' => $event->getParentId()
            ]
        );

        // Convert the saved EloquentEvent back to the domain Event entity
        return new DomainEvent(
            $eloquentEvent->id,
            $eloquentEvent->title,
            $eloquentEvent->description,
            new EventDateRange(
                new \DateTime($eloquentEvent->start),
                new \DateTime($eloquentEvent->end)
            ),
            $eloquentEvent->recurring_pattern ? new RecurringPattern(
                $eloquentEvent->frequency,
                new \DateTime($eloquentEvent->repeat_until)
            ) : null,
            $eloquentEvent->parent_id
        );
    }

    public function findOverlapping(EventDateRange $dateRange): array
    {
        $events = EloquentEvent::where(function ($query) use ($dateRange) {
            $query->where('start', '<', $dateRange->getEnd()->format('Y-m-d H:i:s'))
                ->where('end', '>', $dateRange->getStart()->format('Y-m-d H:i:s'));
        })->get();

        // $events = EloquentEvent::where(function ($query) use ($dateRange) {
        //     $query->where(function ($q) use ($dateRange) {
        //         $q->where('start', '<', $dateRange->getEnd()->format('Y-m-d H:i:s'))
        //             ->where('end', '>', $dateRange->getStart()->format('Y-m-d H:i:s'));
        //     });
        // })->get();

        return $events->map(function (EloquentEvent $eloquentEvent) {
            return new DomainEvent(
                $eloquentEvent->id,
                $eloquentEvent->title,
                $eloquentEvent->description,
                new EventDateRange($eloquentEvent->start, $eloquentEvent->end),
                $eloquentEvent->recurring_pattern ? new RecurringPattern($eloquentEvent->frequency, $eloquentEvent->repeat_until) : null,
                $eloquentEvent->parent_id
            );
        })->toArray();
    }

    public function findById(int $id): ?DomainEvent
    {
        $eloquentEvent = EloquentEvent::find($id);

        if (!$eloquentEvent) {
            return null;
        }

        return new DomainEvent(
            $eloquentEvent->id,
            $eloquentEvent->title,
            $eloquentEvent->description,
            new EventDateRange($eloquentEvent->start, $eloquentEvent->end),
            $eloquentEvent->recurring_pattern ? new RecurringPattern($eloquentEvent->frequency, $eloquentEvent->repeat_until) : null,
            $eloquentEvent->parent_id
        );
    }

    public function findByDateRange(string $start, string $end, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        return EloquentEvent::whereBetween('start', [$start, $end])
            ->orWhereBetween('end', [$start, $end])
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function deleteById(int $id): void
    {
        $eloquentEvent = EloquentEvent::find($id);

        if (!$eloquentEvent) {
            throw new EventNotFoundException();
        }

        // Delete recurring events related to this event
        if ($eloquentEvent->parent_id) {
            EloquentEvent::where('parent_id', $id)->delete();
        }

        EloquentEvent::destroy($id);
    }

    public function deleteOccurrencesByParentId(int $parentId): void
    {
        // Delete occurrences from the database where parent_id matches the given ID
        EloquentEvent::where('parent_id', $parentId)->delete();
    }
}
