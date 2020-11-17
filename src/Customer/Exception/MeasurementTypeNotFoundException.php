<?php

namespace App\Customer\Exception;

class MeasurementTypeNotFoundException extends \Exception
{
    protected $message = "Measurement Type not found";
}