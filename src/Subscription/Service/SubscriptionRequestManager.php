<?php

declare(strict_types=1);

namespace App\Subscription\Service;

use App\Customer\Entity\Customer;
use App\Customer\Provider\CustomerProvider;
use App\Customer\Service\CustomerRequestManager;
use App\Subscription\Entity\Subscription;
use App\Subscription\Request\SubscriptionRequest;
use App\Subscription\Request\SubscriptionReviewRequest;
use App\Vendor\Provider\VendorPlanProvider;
use Ramsey\Uuid\Uuid;

class SubscriptionRequestManager
{
    public function __construct(
        private SubscriptionManager $subscriptionManager,
        private CustomerProvider $customerProvider,
        private CustomerRequestManager $customerRequestManager,
        private VendorPlanProvider $vendorPlanProvider
    ) {
    }

    public function createFromCustomerRequest(
        Customer $customer,
        SubscriptionRequest $subscriptionRequest,
        ?string $ipAddress = null
    ): Subscription {
        $subscription = new Subscription();

        $this->mapDataFromRequest($subscription, $subscriptionRequest, $ipAddress);

        $subscription->setCustomer($customer);

        $this->subscriptionManager->create($subscription);

        return $subscription;
    }

    public function createFromRequest(SubscriptionRequest $subscriptionRequest): Subscription
    {
        $subscription = new Subscription();
        $this->mapDataFromRequest($subscription, $subscriptionRequest);

        $this->subscriptionManager->create($subscription);

        return $subscription;
    }

    public function review(Subscription $subscription, SubscriptionReviewRequest $subscriptionReviewRequest): void
    {
        if ($subscriptionReviewRequest->isApproved) {
            $this->subscriptionManager->approve($subscription, $subscriptionReviewRequest->reviewNotes);
        } else {
            $this->subscriptionManager->reject($subscription, $subscriptionReviewRequest->reviewNotes);
        }
    }

    private function mapDataFromRequest(
        Subscription $subscription,
        SubscriptionRequest $subscriptionRequest,
        ?string $ipAddress = null
    ): void {
        if (null !== $subscriptionRequest->customer) {
            $customer = $this->customerRequestManager->createFromRequest($subscriptionRequest->customer, $ipAddress);

            $subscription->setCustomer($customer);
        }

        if (null !== $subscriptionRequest->customerId && null === $subscription->getCustomer()) {
            $customer = $this->customerProvider->get(Uuid::fromString($subscriptionRequest->customerId));

            $subscription->setCustomerId($customer->getId());
            $subscription->setCustomer($customer);
        }

        $subscription->setVendorPlan(
            $this->vendorPlanProvider->get(Uuid::fromString($subscriptionRequest->vendorPlanId))
        );
    }
}
