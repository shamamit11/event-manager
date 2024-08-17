<?php

namespace App\Domains\Calendar\Exceptions;

use Exception;

class OverlappingEventException extends Exception
{
    protected $message = 'The event overlaps with an existing event.';

    public function __construct(string $message = null)
    {
        if ($message) {
            $this->message = $message;
        }
        parent::__construct($this->message);
    }
}
