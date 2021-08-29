<?php

declare(strict_types=1);

namespace App\Customer\Exception;

class CustomerMeasurementItemInvalidUnitException extends \Exception
{
    protected $message = 'Invalid measurement unit';
}
