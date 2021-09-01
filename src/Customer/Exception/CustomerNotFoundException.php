<?php

declare(strict_types=1);

namespace App\Customer\Exception;

use App\Core\Exception\ResourceNotFoundException;

class CustomerNotFoundException extends ResourceNotFoundException
{
    protected $message = 'Customer not found';
}
