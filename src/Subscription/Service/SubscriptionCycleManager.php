<?php

namespace App\Subscription\Service;

use App\Subscription\Entity\Subscription;
use App\Subscription\Entity\SubscriptionCycle;
use App\Subscription\Repository\SubscriptionCycleRepository;

class SubscriptionCycleManager
{
    private SubscriptionCycleRepository $subscriptionCycleRepository;

    public function __construct(
        SubscriptionCycleRepository $subscriptionCycleRepository
    ) {
        $this->subscriptionCycleRepository = $subscriptionCycleRepository;
    }

    public function create(
        Subscription $subscription,
        \DateTimeInterface $startsAt,
        \DateTimeInterface $endsAt
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
