<?php

declare(strict_types=1);

namespace App\Customer\Exception;

class CustomerMeasurementInvalidTakenAtException extends \Exception
{
    protected $message = 'Invalid takenAt value';
}
