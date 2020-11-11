<?php

namespace App\Vendor\Exception;

class VendorNotFoundException extends \Exception
{
    protected $message = "Vendor not found";
}