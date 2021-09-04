<?php

declare(strict_types=1);

namespace App\Customer\Exception;

use App\Core\Exception\InvalidInputException;

class PhotoTypeAlreadyExistsException extends InvalidInputException
{
    protected $message = 'Photo Type already exists';
}
