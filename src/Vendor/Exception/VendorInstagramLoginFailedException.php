<?php

declare(strict_types=1);

namespace App\Vendor\Exception;

use App\Core\Exception\InvalidInputException;

class VendorInstagramLoginFailedException extends InvalidInputException
{
    /**
     * @var string
     */
    protected $message = 'Unable to authenticate user with instagram access token';
}
