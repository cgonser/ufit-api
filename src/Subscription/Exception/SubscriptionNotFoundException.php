<?php

namespace App\Subscription\Exception;

class SubscriptionNotFoundException extends \Exception
{
    protected $message = "Subscription not found";
}