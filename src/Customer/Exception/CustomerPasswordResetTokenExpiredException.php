<?php

declare(strict_types=1);

namespace App\Customer\Exception;

use App\Core\Exception\InvalidInputException;

class CustomerPasswordResetTokenExpiredException extends InvalidInputException
{
    protected $message = 'Customer password reset token expired';
}
