<?php

declare(strict_types=1);

namespace App\Subscription\Exception;

use App\Core\Exception\ResourceNotFoundException;

class SubscriptionNotFoundException extends ResourceNotFoundException
{
    /**
     * @var string
     */
    protected $message = 'Subscription not found';
}
