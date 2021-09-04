<?php

declare(strict_types=1);

namespace App\Subscription\Message;

use Ramsey\Uuid\UuidInterface;

class SubscriptionApprovedEvent
{
    /**
     * @var string
     */
    public const NAME = 'subscription.approved';

    public function __construct(protected ?UuidInterface $subscriptionId = null)
    {
    }

    public function getSubscriptionId(): ?UuidInterface
    {
        return $this->subscriptionId;
    }
}
