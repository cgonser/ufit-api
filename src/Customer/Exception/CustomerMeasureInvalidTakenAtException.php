<?php

namespace App\Customer\Exception;

class CustomerMeasureInvalidTakenAtException extends \Exception
{
    protected $message = "Invalid takenAt value";
}
