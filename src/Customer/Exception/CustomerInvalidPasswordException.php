<?php

declare(strict_types=1);

namespace App\Customer\Exception;

use App\Core\Exception\InvalidInputException;

class CustomerInvalidPasswordException extends InvalidInputException
{
    protected $message = 'Invalid password';
}
