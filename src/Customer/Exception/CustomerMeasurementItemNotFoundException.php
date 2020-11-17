<?php

namespace App\Customer\Exception;

class CustomerMeasurementItemNotFoundException extends \Exception
{
    protected $message = "Measurement item not found";
}