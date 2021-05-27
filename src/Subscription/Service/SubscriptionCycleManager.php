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

    public function createFirst(Subscription $subscription)
    {
        $subscriptionCycle = new SubscriptionCycle();
        $subscriptionCycle->setSubscription($subscription);
        $subscriptionCycle->setPrice($subscription->getPrice());
        $subscriptionCycle->setStartsAt(); // todo: to be finished

        $this->subscriptionCycleRepository->save($subscription);
    }
}
