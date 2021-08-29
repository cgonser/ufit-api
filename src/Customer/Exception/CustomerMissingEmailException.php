<?php

declare(strict_types=1);

namespace App\Customer\Exception;

use App\Core\Exception\InvalidInputException;

class CustomerMissingEmailException extends InvalidInputException
{
    protected $message = 'E-mail is missing';
}
