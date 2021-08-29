<?php

declare(strict_types=1);

namespace App\Customer\Exception;

class CustomerAlreadyExistsException extends \Exception
{
    protected $message = 'Customer already exists';
}
