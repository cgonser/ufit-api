<?php

declare(strict_types=1);

namespace App\Subscription\Message;

use Ramsey\Uuid\UuidInterface;

class SubscriptionRejectedEvent
{
    public const NAME = 'subscription.rejected';

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
