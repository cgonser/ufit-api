<?php

declare(strict_types=1);

namespace App\Customer\Exception;

use App\Core\Exception\InvalidInputException;

class CustomerMissingNameException extends InvalidInputException
{
    protected $message = 'Name is missing';
}
