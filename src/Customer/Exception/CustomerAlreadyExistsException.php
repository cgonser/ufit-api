<?php

declare(strict_types=1);

namespace App\Customer\Exception;

use App\Core\Exception\InvalidInputException;

class CustomerAlreadyExistsException extends InvalidInputException
{
    protected $message = 'Customer already exists';
}
