<?php

declare(strict_types=1);

namespace App\Customer\Exception;

class MeasurementTypeAlreadyExistsException extends \Exception
{
    protected $message = 'Measurement Type already exists';
}
