<?php

declare(strict_types=1);

namespace App\Subscription\Message;

use Ramsey\Uuid\UuidInterface;

class SubscriptionCreatedEvent
{
    /**
     * @var string
     */
    public const NAME = 'subscription.created';

    public function __construct(protected ?UuidInterface $subscriptionId = null)
    {
    }

    public function getSubscriptionId(): ?UuidInterface
    {
        return $this->subscriptionId;
    }
}
