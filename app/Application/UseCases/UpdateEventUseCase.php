<?php

namespace App\Application\UseCases;

use App\Domains\Calendar\Entities\Event;
use App\Domains\Calendar\Repositories\EventRepositoryInterface;
use App\Domains\Calendar\ValueObjects\EventDateRange;
use App\Domains\Calendar\ValueObjects\RecurringPattern;
use App\Domains\Calendar\Exceptions\OverlappingEventException;
use DateTime;
use Illuminate\Support\Facades\DB;

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
        $occurrences = [];

        if ($recurringPattern) {
            if (empty($frequency) || empty($repeat_until)) {
                throw new \InvalidArgumentException('Frequency and repeat_until must be provided for recurring events.');
            }
            $repeatUntilDateTime = new DateTime($repeat_until);
            $recurringPatternObject = new RecurringPattern($frequency, $repeatUntilDateTime);

            $occurrences = $this->generateOccurrences($title, $description, $dateRange, $recurringPatternObject);
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

        DB::transaction(function () use ($updatedEvent, $existingEvent, $occurrences) {
            // Delete existing occurrences
            if ($existingEvent->getRecurringPattern()) {
                $this->eventRepository->deleteById($existingEvent->id);
            }

            // Save updated event
            $this->eventRepository->save($updatedEvent);

            // Save new occurrences
            foreach ($occurrences as $occurrence) {
                $this->eventRepository->save($occurrence);
            }
        });

        $this->eventRepository->save($updatedEvent);

        foreach ($occurrences as $occurrence) {
            $this->eventRepository->save($occurrence);
        }
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
