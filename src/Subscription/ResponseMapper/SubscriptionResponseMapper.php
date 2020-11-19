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
        $subscriptionDto->expiresAt = $subscription->getExpiresAt()
            ? $subscription->getExpiresAt()->format(\DateTimeInterface::ISO8601)
            : null;
        $subscriptionDto->reviewedAt = $subscription->getReviewedAt()
            ? $subscription->getReviewedAt()->format(\DateTimeInterface::ISO8601)
            : null;
        $subscriptionDto->isApproved = $subscription->getIsApproved();

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
