<?php

declare(strict_types=1);

namespace App\Subscription\Message;

use Ramsey\Uuid\UuidInterface;

class SubscriptionRejectedEvent
{
    /**
     * @var string
     */
    public const NAME = 'subscription.rejected';

    public function __construct(protected UuidInterface $subscriptionId)
    {
    }

    public function getSubscriptionId(): ?UuidInterface
    {
        return $this->subscriptionId;
    }
}
