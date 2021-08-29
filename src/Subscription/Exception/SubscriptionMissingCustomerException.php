<?php

declare(strict_types=1);

namespace App\Subscription\Exception;

use App\Core\Exception\InvalidInputException;

class SubscriptionMissingCustomerException extends InvalidInputException
{
    protected $message = 'Customer is missing';
}
