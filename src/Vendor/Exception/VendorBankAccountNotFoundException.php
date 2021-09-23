<?php

declare(strict_types=1);

namespace App\Vendor\Exception;

use App\Core\Exception\ResourceNotFoundException;

class VendorBankAccountNotFoundException extends ResourceNotFoundException
{
    /**
     * @var string
     */
    protected $message = 'Vendor bank account not found';
}
