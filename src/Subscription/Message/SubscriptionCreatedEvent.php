<?php

namespace App\Subscription\Message;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class SubscriptionCreatedEvent
{
    public const NAME = 'subscription.created';

    protected ?UuidInterface $subscriptionId = null;

    public function __construct(UuidInterface $subscriptionId)
    {
        $this->subscriptionId = $subscriptionId;
    }

    public function getSubscriptionId(): ?UuidInterface
    {
        return $this->subscriptionId;
    }
}
