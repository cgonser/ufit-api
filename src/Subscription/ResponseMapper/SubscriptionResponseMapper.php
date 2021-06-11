<?php

namespace App\Subscription\ResponseMapper;

use App\Customer\Entity\Customer;
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
            ? $subscription->getExpiresAt()->format(\DateTimeInterface::ATOM)
            : null;
        $subscriptionDto->price = $subscription->getPrice()->toFloat();
        $subscriptionDto->reviewedAt = $subscription->getReviewedAt()
            ? $subscription->getReviewedAt()->format(\DateTimeInterface::ATOM)
            : null;
        $subscriptionDto->isApproved = $subscription->isApproved();
        $subscriptionDto->isRecurring = $subscription->isRecurring();
        $subscriptionDto->isActive = $subscription->isActive();

        if ($mapRelations) {
            $subscriptionDto->vendorPlan = $this->vendorPlanResponseMapper->map($subscription->getVendorPlan());
            $subscriptionDto->customer = $this->customerResponseMapper->map($subscription->getCustomer());
        } else {
            $subscriptionDto->vendorPlanId = $subscription->getVendorPlan()->getId()->toString();
            $subscriptionDto->customerId = $subscription->getCustomer()->getId()->toString();
        }

        $subscriptionDto->cancelledAt = $subscription->getCancelledAt()
            ? $subscription->getCancelledAt()->format(\DateTimeInterface::ATOM)
            : null;

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

    public function mapMultipleCustomers(array $customers): array
    {
        $customerDtos = [];

        /** @var Customer $customer */
        foreach ($customers as $customer) {
            $customerDto = $this->customerResponseMapper->map($customer);

            $customerDto->subscriptions = $this->mapMultiple($customer->getActiveSubscriptions()->toArray(), true);

            $customerDtos[] = $customerDto;
        }

        return $customerDtos;
    }
}
