<?php

declare(strict_types=1);

namespace App\Vendor\Exception;

use Exception;

class VendorFacebookLoginFailedException extends Exception
{
    /**
     * @var string
     */
    protected $message = 'Unable to authenticate user with facebook access token';
}
