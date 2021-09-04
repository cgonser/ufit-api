<?php

declare(strict_types=1);

namespace App\Customer\Exception;

use App\Core\Exception\InvalidInputException;

class CustomerEmailAddressInUseException extends InvalidInputException
{
    protected $message = 'E-mail address already in use';
}
