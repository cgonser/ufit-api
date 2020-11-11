<?php

namespace App\Vendor\Exception;

class VendorPlanInvalidDurationException extends \Exception
{
    protected $message = "The duration informed is not valid";
}