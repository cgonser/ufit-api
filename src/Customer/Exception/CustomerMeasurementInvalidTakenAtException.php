<?php

namespace App\Customer\Exception;

class CustomerMeasurementInvalidTakenAtException extends \Exception
{
    protected $message = "Invalid takenAt value";
}
