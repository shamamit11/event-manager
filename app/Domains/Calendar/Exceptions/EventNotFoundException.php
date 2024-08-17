<?php

namespace App\Domains\Calendar\Exceptions;

use Exception;

class EventNotFoundException extends Exception
{
    protected $message = 'The event does not exists.';
}
