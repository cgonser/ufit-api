<?php

namespace App\Customer\Exception;

class CustomerMeasurementNotFoundException extends \Exception
{
    protected $message = "Measurement not found";
}