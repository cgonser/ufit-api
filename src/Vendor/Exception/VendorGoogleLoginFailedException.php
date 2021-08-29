<?php

declare(strict_types=1);

namespace App\Vendor\Exception;

use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class VendorGoogleLoginFailedException extends UnauthorizedHttpException
{
    /**
     * @var string
     */
    protected $message = 'Unable to authenticate user with google access token';
}
