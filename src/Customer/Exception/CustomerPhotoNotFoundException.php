<?php

declare(strict_types=1);

namespace App\Customer\Exception;

use App\Core\Exception\ResourceNotFoundException;

class CustomerPhotoNotFoundException extends ResourceNotFoundException
{
    protected $message = 'Photo not found';
}
