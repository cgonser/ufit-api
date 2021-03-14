<?php

namespace App\Vendor\Exception;

use App\Core\Exception\ResourceNotFoundException;

class VendorBankAccountNotFoundException extends ResourceNotFoundException
{
    protected $message = 'Vendor bank account not found';
}
