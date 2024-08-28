<?php

namespace App\Application\UseCases;

use App\Domains\Calendar\Entities\Event;
use App\Domains\Calendar\Repositories\EventRepositoryInterface;
use App\Domains\Calendar\ValueObjects\EventDateRange;
use App\Domains\Calendar\ValueObjects\RecurringPattern;
use App\Domains\Calendar\Exceptions\OverlappingEventException;
use App\Application\Services\OccurrenceGenerator;
use DateTime;

class UpdateEventUseCase
{
    private EventRepositoryInterface $eventRepository;
    private OccurrenceGenerator $occurrenceGenerator;

    public function __construct(EventRepositoryInterface $eventRepository, OccurrenceGenerator $occurrenceGenerator)
    {
        $this->eventRepository = $eventRepository;
        $this->occurrenceGenerator = $occurrenceGenerator;
    }

    public function execute(int $id, string $title, ?string $description, string $start, string $end, bool $recurringPattern, ?string $frequency, ?string $repeat_until, ?int $parentId = null): Event
    {
        $existingEvent = $this->eventRepository->findById($id);

        if (!$existingEvent) {
            throw new \InvalidArgumentException("Event not found.");
        }

        $startDateTime = new DateTime($start);
        $endDateTime = new DateTime($end);

        $dateRange = new EventDateRange($startDateTime, $endDateTime);

        $recurringPatternObject = null;
        $occurrences = [];

        if ($recurringPattern) {
            if (empty($frequency) || empty($repeat_until)) {
                throw new \InvalidArgumentException('Frequency and repeat_until must be provided for recurring events.');
            }
            $repeatUntilDateTime = new DateTime($repeat_until);
            $recurringPatternObject = new RecurringPattern($frequency, $repeatUntilDateTime);

            $occurrences = $this->occurrenceGenerator->generateOccurrences($title, $description, $dateRange, $recurringPatternObject);
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

        $savedEvent = $this->eventRepository->save($updatedEvent);

        if ($recurringPattern) {
            // Delete old occurrences
            if ($existingEvent->getRecurringPattern()) {
                $this->eventRepository->deleteOccurrencesByParentId($existingEvent->id);
            }

            // Save new occurrences
            foreach ($occurrences as $occurrence) {
                $occurrence->setParentId($savedEvent->getId());
                $this->eventRepository->save($occurrence);
            }
        } else {
            // Delete occurrences if the event is no longer recurring
            if ($existingEvent->getRecurringPattern()) {
                $this->eventRepository->deleteOccurrencesByParentId($savedEvent->getId());
            }
        }

        return $savedEvent;
    }
}
