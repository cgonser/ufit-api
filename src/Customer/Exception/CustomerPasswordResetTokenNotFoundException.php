<?php

declare(strict_types=1);

namespace App\Customer\Exception;

use App\Core\Exception\ResourceNotFoundException;

class CustomerPasswordResetTokenNotFoundException extends ResourceNotFoundException
{
    protected $message = 'Customer password reset token not found';
}
