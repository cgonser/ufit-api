<?php

declare(strict_types=1);

namespace App\Vendor\Exception;

use App\Core\Exception\ResourceNotFoundException;

class VendorNotFoundException extends ResourceNotFoundException
{
    /**
     * @var string
     */
    protected $message = 'Vendor not found';
}
