<?php

declare(strict_types=1);

namespace App\Customer\Exception;

use App\Core\Exception\ResourceNotFoundException;

class MeasurementTypeNotFoundException extends ResourceNotFoundException
{
    protected $message = 'Measurement Type not found';
}
