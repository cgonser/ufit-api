<?php

declare(strict_types=1);

namespace App\Customer\Exception;

use App\Core\Exception\ResourceNotFoundException;

class CustomerSocialNetworkNotFoundException extends ResourceNotFoundException
{
    protected $message = 'Customer social network integration not found';
}
