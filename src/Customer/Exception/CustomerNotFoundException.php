<?php

declare(strict_types=1);

namespace App\Customer\Exception;

class CustomerNotFoundException extends \Exception
{
    protected $message = 'Customer not found';
}
