<?php

namespace App\Customer\Exception;

use App\Core\Exception\InvalidInputException;

class CustomerMissingEmailException extends InvalidInputException
{
    protected $message = 'E-mail is missing';
}
