<?php

declare(strict_types=1);

namespace App\Customer\Exception;

class CustomerInvalidBirthDateException extends \Exception
{
    protected $message = 'Invalid birth date';
}
