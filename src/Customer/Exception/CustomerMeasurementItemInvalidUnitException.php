<?php

declare(strict_types=1);

namespace App\Customer\Exception;

use App\Core\Exception\InvalidInputException;

class CustomerMeasurementItemInvalidUnitException extends InvalidInputException
{
    protected $message = 'Invalid measurement unit';
}
