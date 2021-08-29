<?php

declare(strict_types=1);

namespace App\Vendor\Exception;

use Exception;

class VendorInstagramLoginMissingEmailException extends Exception
{
    /**
     * @var string
     */
    protected $message = 'Missing vendor e-mail to be able to successfully authenticate';
}
