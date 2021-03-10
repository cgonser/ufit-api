<?php

namespace App\Customer\Exception;

use App\Core\Exception\ResourceNotFoundException;

class CustomerMeasurementNotFoundException extends ResourceNotFoundException
{
    protected $message = "Measurement not found";
}