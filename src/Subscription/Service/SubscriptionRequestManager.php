<?php

namespace App\Subscription\Service;

use App\Customer\Entity\Customer;
use App\Customer\Provider\CustomerProvider;
use App\Customer\Service\CustomerService;
use App\Subscription\Entity\Subscription;
use App\Subscription\Exception\SubscriptionMissingCustomerException;
use App\Subscription\Request\SubscriptionRequest;
use App\Subscription\Request\SubscriptionReviewRequest;
use App\Vendor\Provider\VendorPlanProvider;
use Ramsey\Uuid\Uuid;

class SubscriptionRequestManager
{
    private SubscriptionManager $subscriptionManager;

    private CustomerProvider $customerProvider;

    private CustomerService $customerManager;

    private VendorPlanProvider $vendorPlanProvider;

    public function __construct(
        SubscriptionManager $subscriptionManager,
        CustomerProvider $customerProvider,
        CustomerService $customerManager,
        VendorPlanProvider $vendorPlanProvider
    ) {
        $this->subscriptionManager = $subscriptionManager;
        $this->customerProvider = $customerProvider;
        $this->customerManager = $customerManager;
        $this->vendorPlanProvider = $vendorPlanProvider;
    }

    public function createFromCustomerRequest(Customer $customer, SubscriptionRequest $subscriptionRequest): Subscription
    {
        $subscription = new Subscription();
        $subscription->setCustomer($customer);
        $this->mapDataFromRequest($subscription, $subscriptionRequest);

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

    private function mapDataFromRequest(Subscription $subscription, SubscriptionRequest $subscriptionRequest)
    {
        if (null !== $subscriptionRequest->customer) {
            $customer = $this->customerManager->create($subscriptionRequest->customer);

            $subscription->setCustomer($customer);
        }

        if (null === $subscription->getCustomer() && null !== $subscriptionRequest->customerId) {
            $customer = $this->customerProvider->get(Uuid::fromString($subscriptionRequest->customerId));

            $subscription->setCustomerId($customer->getId());
            $subscription->setCustomer($customer);
        }

        $subscription->setVendorPlan(
            $this->vendorPlanProvider->get(Uuid::fromString($subscriptionRequest->vendorPlanId))
        );
    }
}