<?php

declare(strict_types=1);

namespace App\Customer\Exception;

class PhotoTypeAlreadyExistsException extends \Exception
{
    protected $message = 'Photo Type already exists';
}
