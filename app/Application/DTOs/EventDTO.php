<?php

namespace App\Application\DTOs;

class EventDTO
{
    public function __construct(
        public string $title,
        public ?string $description,
        public string $start,
        public string $end,
        public bool $recurringPattern,
        public ?string $frequency,
        public ?string $repeat_until
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->start = $start;
        $this->end = $end;
        $this->recurringPattern = $recurringPattern;
        $this->frequency = $frequency;
        $this->repeat_until = $repeat_until;
    }
}
