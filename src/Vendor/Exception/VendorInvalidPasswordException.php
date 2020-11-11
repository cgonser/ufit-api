<?php

namespace App\Vendor\Exception;

class VendorInvalidPasswordException extends \Exception
{
    protected $message = "Invalid password";
}