<?php

declare(strict_types=1);

namespace App\Customer\Exception;

class CustomerInvalidPasswordException extends \Exception
{
    protected $message = 'Invalid password';
}
