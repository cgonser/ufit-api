<?php

declare(strict_types=1);

namespace App\Customer\Exception;

class CustomerMeasurementItemNotFoundException extends \Exception
{
    protected $message = 'Measurement item not found';
}
