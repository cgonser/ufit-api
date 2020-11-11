<?php

namespace App\Vendor\Exception;

class VendorAlreadyExistsException extends \Exception
{
    protected $message = "Vendor already exists";
}