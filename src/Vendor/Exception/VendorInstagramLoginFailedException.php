<?php

namespace App\Vendor\Exception;

class VendorInstagramLoginFailedException extends \Exception
{
    protected $message = 'Unable to authenticate user with instagram access token';
}