<?php

declare(strict_types=1);

namespace App\Customer\Exception;

use App\Core\Exception\InvalidInputException;

class MeasurementTypeAlreadyExistsException extends InvalidInputException
{
    protected $message = 'Measurement Type already exists';
}
