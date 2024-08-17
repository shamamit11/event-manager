<?php

namespace App\Domains\Calendar\ValueObjects;

use DateTime;

class RecurringPattern
{
    public string $frequency;
    public DateTime $repeat_until;

    public function __construct(string $frequency, DateTime $repeat_until)
    {
        $validFrequencies = ['daily', 'weekly', 'monthly', 'yearly'];
        if (!in_array($frequency, $validFrequencies)) {
            throw new \InvalidArgumentException("Invalid frequency value.");
        }
        $this->frequency = $frequency;
        $this->repeat_until = $repeat_until;
    }

    public function getFrequency(): string
    {
        return $this->frequency;
    }

    public function getRepeatUntil(): DateTime
    {
        return $this->repeat_until;
    }
}
