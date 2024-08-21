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
        $occurrences = [];

        if ($recurringPattern) {
            if (empty($frequency) || empty($repeat_until)) {
                throw new \InvalidArgumentException('Frequency and repeat_until must be provided for recurring events.');
            }
            $repeatUntilDateTime = new DateTime($repeat_until);
            $recurringPatternObject = new RecurringPattern($frequency, $repeatUntilDateTime);

            $occurrences = $this->generateOccurrences($title, $description, $dateRange, $recurringPatternObject);
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

        foreach ($occurrences as $occurrence) {
            $this->eventRepository->save($occurrence);
        }

        return $savedEvent;
    }

    private function generateOccurrences(string $title, ?string $description, EventDateRange $dateRange, RecurringPattern $recurringPattern): array
    {
        $occurrences = [];
        $startDateTime = clone $dateRange->getStart();
        $endDateTime = clone $dateRange->getEnd();
        $intervalSpec = $this->getDateIntervalSpec($recurringPattern->getFrequency());

        while ($startDateTime <= $recurringPattern->getRepeatUntil()) {
            $newDateRange = new EventDateRange(clone $startDateTime, clone $endDateTime);
            $occurrences[] = new Event(null, $title, $description, $newDateRange, null);

            $startDateTime->add(new \DateInterval($intervalSpec));
            $endDateTime->add(new \DateInterval($intervalSpec));
        }

        return $occurrences;
    }

    private function getDateIntervalSpec(string $frequency): string
    {
        switch ($frequency) {
            case 'daily':
                return 'P1D';
            case 'weekly':
                return 'P1W';
            case 'monthly':
                return 'P1M';
            case 'yearly':
                return 'P1Y';
            default:
                throw new \Exception("Invalid frequency: $frequency");
        }
    }
}
