<?php

namespace App\Vendor\Exception;

class VendorEmailAddressInUseException extends \Exception
{
    protected $message = "E-mail address already in use";
}