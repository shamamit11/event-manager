<?php

namespace App\Application\UseCases;

use App\Domains\Calendar\Entities\Event;
use App\Domains\Calendar\Repositories\EventRepositoryInterface;
use App\Domains\Calendar\ValueObjects\EventDateRange;
use App\Domains\Calendar\ValueObjects\RecurringPattern;
use App\Domains\Calendar\Exceptions\OverlappingEventException;
use App\Application\Services\OccurrenceGenerator;
use DateTime;

class CreateEventUseCase
{
    private EventRepositoryInterface $eventRepository;
    private OccurrenceGenerator $occurrenceGenerator;

    public function __construct(EventRepositoryInterface $eventRepository, OccurrenceGenerator $occurrenceGenerator)
    {
        $this->eventRepository = $eventRepository;
        $this->occurrenceGenerator = $occurrenceGenerator;
    }

    public function execute(string $title, ?string $description, string $start, string $end, bool $recurringPattern, ?string $frequency, ?string $repeat_until, ?int $parentId = null): Event
    {
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

        $newEvent = new Event(
            null,
            $title,
            $description,
            $dateRange,
            $recurringPatternObject
        );

        $overlappingEvents = $this->eventRepository->findOverlapping($dateRange);

        if (!empty($overlappingEvents)) {
            throw new OverlappingEventException("The event overlaps with an existing event.");
        }

        $savedEvent = $this->eventRepository->save($newEvent);

        if ($recurringPattern) {
            // Update the parent_id for all occurrences and save
            foreach ($occurrences as $occurrence) {
                $occurrence->setParentId($savedEvent->getId());
                $this->eventRepository->save($occurrence);
            }
        }

        return $savedEvent;
    }
}
