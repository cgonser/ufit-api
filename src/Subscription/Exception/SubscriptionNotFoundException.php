<?php

namespace App\Subscription\Exception;

use App\Core\Exception\ResourceNotFoundException;

class SubscriptionNotFoundException extends ResourceNotFoundException
{
    protected $message = 'Subscription not found';
}
