<?php

namespace App\Application\UseCases;

use App\Domains\Calendar\Entities\Event;
use App\Domains\Calendar\Repositories\EventRepositoryInterface;
use App\Domains\Calendar\ValueObjects\EventDateRange;
use App\Domains\Calendar\ValueObjects\RecurringPattern;
use App\Domains\Calendar\Exceptions\OverlappingEventException;
use DateTime;

class CreateEventUseCase
{
    private EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function execute(string $title, ?string $description, string $start, string $end, bool $recurringPattern, ?string $frequency, ?string $repeat_until): Event
    {
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

        return $this->eventRepository->save($newEvent);
    }
}
