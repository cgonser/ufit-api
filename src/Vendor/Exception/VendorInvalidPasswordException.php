<?php

declare(strict_types=1);

namespace App\Vendor\Exception;

use Exception;

class VendorInvalidPasswordException extends Exception
{
    /**
     * @var string
     */
    protected $message = 'Invalid password';
}
