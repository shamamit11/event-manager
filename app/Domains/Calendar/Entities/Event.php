<?php

namespace App\Domains\Calendar\Entities;

use App\Domains\Calendar\ValueObjects\EventDateRange;
use App\Domains\Calendar\ValueObjects\RecurringPattern;

class Event
{
    public ?int $id;
    public string $title;
    public ?string $description;
    public EventDateRange $dateRange;
    public ?RecurringPattern $recurringPattern;
    public ?int $parentId;

    public function __construct(?int $id, string $title, ?string $description, EventDateRange $dateRange, ?RecurringPattern $recurringPattern = null, ?int $parentId = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->dateRange = $dateRange;
        $this->recurringPattern = $recurringPattern;
        $this->parentId = $parentId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateRange(): EventDateRange
    {
        return $this->dateRange;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function setParentId(?int $parentId): void
    {
        $this->parentId = $parentId;
    }

    public function overlapsWith(Event $event): bool
    {
        return $this->dateRange->overlapsWith($event->dateRange);
    }

    public function hasRecurringPattern(): bool
    {
        return $this->recurringPattern !== null;
    }

    public function getRecurringPattern(): ?RecurringPattern
    {
        return $this->recurringPattern;
    }
}
