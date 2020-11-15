<?php

namespace App\Subscription\ResponseMapper;

use App\Subscription\Dto\SubscriptionDto;
use App\Subscription\Entity\Subscription;

class SubscriptionResponseMapper
{
    public function map(Subscription $subscription): SubscriptionDto
    {
        $subscriptionDto = new SubscriptionDto();
        $subscriptionDto->id = $subscription->getId()->toString();
        $subscriptionDto->vendorPlanId = $subscription->getVendorPlan()->getId()->toString();
        $subscriptionDto->customerId = $subscription->getCustomer()->getId()->toString();
        $subscriptionDto->expiresAt = $subscription->getExpiresAt()->format(DATE_ISO8601);

        return $subscriptionDto;
    }

    public function mapMultiple(array $subscriptions): array
    {
        $subscriptionDtos = [];

        foreach ($subscriptions as $subscription) {
            $subscriptionDtos[] = $this->map($subscription);
        }

        return $subscriptionDtos;
    }
}
