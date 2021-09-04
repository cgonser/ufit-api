<?php

declare(strict_types=1);

namespace App\Subscription\ResponseMapper;

use DateTimeInterface;
use App\Customer\Dto\CustomerDto;
use App\Customer\Entity\Customer;
use App\Customer\ResponseMapper\CustomerResponseMapper;
use App\Subscription\Dto\SubscriptionDto;
use App\Subscription\Entity\Subscription;
use App\Vendor\ResponseMapper\VendorPlanResponseMapper;

class SubscriptionResponseMapper
{
    public function __construct(
        private VendorPlanResponseMapper $vendorPlanResponseMapper,
        private CustomerResponseMapper $customerResponseMapper
    ) {
    }

    public function map(Subscription $subscription, bool $mapRelations = false): SubscriptionDto
    {
        $subscriptionDto = new SubscriptionDto();
        $subscriptionDto->id = $subscription->getId()->toString();
        $subscriptionDto->expiresAt = $subscription->getExpiresAt()?->format(DateTimeInterface::ATOM);
        $subscriptionDto->validFrom = $subscription->getValidFrom()?->format(DateTimeInterface::ATOM);
        $subscriptionDto->price = $subscription->getPrice()->toFloat();
        $subscriptionDto->reviewedAt = $subscription->getReviewedAt()?->format(DateTimeInterface::ATOM);
        $subscriptionDto->isApproved = $subscription->isApproved();
        $subscriptionDto->isRecurring = $subscription->isRecurring();
        $subscriptionDto->isActive = $subscription->isActive();
        $subscriptionDto->cancelledAt = $subscription->getCancelledAt()?->format(DateTimeInterface::ATOM);

        if ($mapRelations) {
            $subscriptionDto->vendorPlan = $this->vendorPlanResponseMapper->map($subscription->getVendorPlan());
            $subscriptionDto->customer = $this->customerResponseMapper->map($subscription->getCustomer());
        } else {
            $subscriptionDto->vendorPlanId = $subscription->getVendorPlan()->getId()->toString();
            $subscriptionDto->customerId = $subscription->getCustomer()->getId()->toString();
        }

        return $subscriptionDto;
    }

    /**
     * @return SubscriptionDto[]
     */
    public function mapMultiple(array $subscriptions, bool $mapRelations = false): array
    {
        $subscriptionDtos = [];

        foreach ($subscriptions as $subscription) {
            $subscriptionDtos[] = $this->map($subscription, $mapRelations);
        }

        return $subscriptionDtos;
    }

    /**
     * @return CustomerDto[]
     */
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
