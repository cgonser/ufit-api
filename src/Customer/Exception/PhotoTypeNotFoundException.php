<?php

declare(strict_types=1);

namespace App\Customer\Exception;

use App\Core\Exception\ResourceNotFoundException;

class PhotoTypeNotFoundException extends ResourceNotFoundException
{
    protected $message = 'Photo Type not found';
}
