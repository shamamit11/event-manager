<?php

namespace App\Application\UseCases;

use App\Domains\Calendar\Entities\Event;
use App\Domains\Calendar\Repositories\EventRepositoryInterface;
use App\Domains\Calendar\ValueObjects\EventDateRange;
use App\Domains\Calendar\ValueObjects\RecurringPattern;
use App\Domains\Calendar\Exceptions\OverlappingEventException;
use DateTime;

class UpdateEventUseCase
{
    private EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function execute(int $id, string $title, ?string $description, string $start, string $end, bool $recurringPattern, ?string $frequency, ?string $repeat_until): void
    {
        $existingEvent = $this->eventRepository->findById($id);

        if (!$existingEvent) {
            throw new \InvalidArgumentException("Event not found.");
        }

        $startDateTime = new DateTime($start);
        $endDateTime = new DateTime($end);

        $dateRange = new EventDateRange($startDateTime, $endDateTime);

        $recurringPatternObject = null;
        if ($recurringPattern) {
            if (empty($frequency) || empty($repeat_until)) {
                throw new \InvalidArgumentException('Frequency and repeat_until must be provided for recurring events.');
            }
            $repeatUntilDateTime = new DateTime($repeat_until);
            $recurringPatternObject = new RecurringPattern($frequency, $repeatUntilDateTime);
        }

        $updatedEvent = new Event(
            $existingEvent->id,
            $title,
            $description,
            $dateRange,
            $recurringPatternObject
        );

        $overlappingEvents = $this->eventRepository->findOverlapping($dateRange);

        if (!empty($overlappingEvents) && !in_array($existingEvent->id, array_column($overlappingEvents, 'id'))) {
            throw new OverlappingEventException("The event overlaps with an existing event.");
        }

        $this->eventRepository->save($updatedEvent);
    }
}
