<?php

namespace App\Application\Services;

use App\Domains\Calendar\Entities\Event;
use App\Domains\Calendar\ValueObjects\EventDateRange;
use App\Domains\Calendar\ValueObjects\RecurringPattern;
use DateInterval;

class OccurrenceGenerator
{
    public function generateOccurrences(string $title, ?string $description, EventDateRange $dateRange, RecurringPattern $recurringPattern): array
    {
        $occurrences = [];
        $startDateTime = clone $dateRange->getStart();
        $endDateTime = clone $dateRange->getEnd();
        $intervalSpec = $this->getDateIntervalSpec($recurringPattern->getFrequency());

        while ($startDateTime <= $recurringPattern->getRepeatUntil()) {
            $newDateRange = new EventDateRange(clone $startDateTime, clone $endDateTime);
            $occurrences[] = new Event(null, $title, $description, $newDateRange, null);

            $startDateTime->add(new DateInterval($intervalSpec));
            $endDateTime->add(new DateInterval($intervalSpec));
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
                throw new \InvalidArgumentException("Invalid frequency: $frequency");
        }
    }
}
