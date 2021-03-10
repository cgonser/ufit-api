<?php

namespace App\Vendor\Exception;

use App\Core\Exception\ResourceNotFoundException;

class VendorPlanNotFoundException extends ResourceNotFoundException
{
    protected $message = "Plan not found";
}