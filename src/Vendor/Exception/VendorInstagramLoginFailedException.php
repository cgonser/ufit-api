<?php

namespace App\Vendor\Exception;

use App\Core\Exception\InvalidInputException;

class VendorInstagramLoginFailedException extends InvalidInputException
{
    protected $message = 'Unable to authenticate user with instagram access token';
}