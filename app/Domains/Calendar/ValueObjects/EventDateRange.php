<?php

namespace App\Domains\Calendar\ValueObjects;

use DateTime;
use InvalidArgumentException;

class EventDateRange
{
    private DateTime $start;
    private DateTime $end;

    public function __construct(DateTime $start, DateTime $end)
    {
        if ($start >= $end) {
            throw new InvalidArgumentException("Start date must be before end date.");
        }

        $this->start = $start;
        $this->end = $end;
    }

    public function overlapsWith(EventDateRange $other): bool
    {
        return $this->start < $other->end && $this->end > $other->start;
    }

    public function getStart(): DateTime
    {
        return $this->start;
    }

    public function getEnd(): DateTime
    {
        return $this->end;
    }
}
