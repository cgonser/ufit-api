<?php

namespace App\Vendor\Exception;

class VendorFacebookLoginFailedException extends \Exception
{
    protected $message = 'Unable to authenticate user with facebook access token';
}