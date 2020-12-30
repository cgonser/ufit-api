<?php

namespace App\Subscription\ResponseMapper;

use App\Customer\ResponseMapper\CustomerResponseMapper;
use App\Subscription\Dto\SubscriptionDto;
use App\Subscription\Entity\Subscription;
use App\Vendor\ResponseMapper\VendorPlanResponseMapper;

class SubscriptionResponseMapper
{
    private VendorPlanResponseMapper $vendorPlanResponseMapper;

    private CustomerResponseMapper $customerResponseMapper;

    public function __construct(
        VendorPlanResponseMapper $vendorPlanResponseMapper,
        CustomerResponseMapper $customerResponseMapper
    ) {
        $this->vendorPlanResponseMapper = $vendorPlanResponseMapper;
        $this->customerResponseMapper = $customerResponseMapper;
    }

    public function map(Subscription $subscription, bool $mapRelations = false): SubscriptionDto
    {
        $subscriptionDto = new SubscriptionDto();
        $subscriptionDto->id = $subscription->getId()->toString();
        $subscriptionDto->expiresAt = $subscription->getExpiresAt()
            ? $subscription->getExpiresAt()->format(\DateTimeInterface::ISO8601)
            : null;
        $subscriptionDto->reviewedAt = $subscription->getReviewedAt()
            ? $subscription->getReviewedAt()->format(\DateTimeInterface::ISO8601)
            : null;
        $subscriptionDto->isApproved = $subscription->getIsApproved();

        if ($mapRelations) {
            $subscriptionDto->vendorPlan = $this->vendorPlanResponseMapper->map($subscription->getVendorPlan());
            $subscriptionDto->customer = $this->customerResponseMapper->map($subscription->getCustomer());
        } else {
            $subscriptionDto->vendorPlanId = $subscription->getVendorPlan()->getId()->toString();
            $subscriptionDto->customerId = $subscription->getCustomer()->getId()->toString();
        }

        return $subscriptionDto;
    }

    public function mapMultiple(array $subscriptions, bool $mapRelations = false): array
    {
        $subscriptionDtos = [];

        foreach ($subscriptions as $subscription) {
            $subscriptionDtos[] = $this->map($subscription, $mapRelations);
        }

        return $subscriptionDtos;
    }
}
