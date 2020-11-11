<?php

namespace App\Vendor\Exception;

class VendorPlanNotFoundException extends \Exception
{
    protected $message = "Plan not found";
}