<?php

namespace App\Customer\Exception;

class CustomerMeasureNotFoundException extends \Exception
{
    protected $message = "Measure not found";
}