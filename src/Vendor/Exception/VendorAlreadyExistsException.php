<?php

declare(strict_types=1);

namespace App\Vendor\Exception;

use Exception;

class VendorAlreadyExistsException extends Exception
{
    /**
     * @var string
     */
    protected $message = 'Vendor already exists';
}
