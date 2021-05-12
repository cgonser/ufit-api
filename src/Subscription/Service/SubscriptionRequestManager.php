<?php

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
    private SubscriptionManager $subscriptionManager;

    private CustomerProvider $customerProvider;

    private CustomerRequestManager $customerManager;

    private VendorPlanProvider $vendorPlanProvider;

    public function __construct(
        SubscriptionManager $subscriptionManager,
        CustomerProvider $customerProvider,
        CustomerRequestManager $customerManager,
        VendorPlanProvider $vendorPlanProvider
    ) {
        $this->subscriptionManager = $subscriptionManager;
        $this->customerProvider = $customerProvider;
        $this->customerManager = $customerManager;
        $this->vendorPlanProvider = $vendorPlanProvider;
    }

    public function createFromCustomerRequest(
        Customer $customer,
        SubscriptionRequest $subscriptionRequest,
        ?string $ipAddress = null
    ): Subscription {
        $subscription = new Subscription();

        $this->mapDataFromRequest($subscription, $subscriptionRequest);

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

    public function review(Subscription $subscription, SubscriptionReviewRequest $subscriptionReviewRequest)
    {
        if (true === $subscriptionReviewRequest->isApproved) {
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
            $customer = $this->customerManager->createFromRequest($subscriptionRequest->customer, $ipAddress);

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
