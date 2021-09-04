<?php

declare(strict_types=1);

namespace App\Customer\Exception;

use App\Core\Exception\ResourceNotFoundException;

class CustomerMeasurementItemNotFoundException extends ResourceNotFoundException
{
    protected $message = 'Measurement item not found';
}
