<?php

namespace App\Vendor\Exception;

use App\Core\Exception\ResourceNotFoundException;

class VendorNotFoundException extends ResourceNotFoundException
{
    protected $message = 'Vendor not found';
}
