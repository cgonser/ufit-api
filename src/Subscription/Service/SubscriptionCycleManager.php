<?php

declare(strict_types=1);

namespace App\Subscription\Service;

use DateTimeInterface;
use App\Subscription\Entity\Subscription;
use App\Subscription\Entity\SubscriptionCycle;
use App\Subscription\Repository\SubscriptionCycleRepository;

class SubscriptionCycleManager
{
    public function __construct(private SubscriptionCycleRepository $subscriptionCycleRepository)
    {
    }

    public function create(
        Subscription $subscription,
        DateTimeInterface $startsAt,
        ?DateTimeInterface $endsAt = null
    ): SubscriptionCycle {
        $subscriptionCycle = new SubscriptionCycle();
        $subscriptionCycle->setSubscription($subscription);
        $subscriptionCycle->setPrice($subscription->getPrice());
        $subscriptionCycle->setStartsAt($startsAt);
        $subscriptionCycle->setEndsAt($endsAt);

        $this->subscriptionCycleRepository->save($subscriptionCycle);

        return $subscriptionCycle;
    }
}
