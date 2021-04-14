<?php

namespace App\Vendor\Exception;

use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class VendorGoogleLoginFailedException extends UnauthorizedHttpException
{
    protected $message = 'Unable to authenticate user with google access token';
}