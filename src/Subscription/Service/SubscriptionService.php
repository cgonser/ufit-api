<?php

namespace App\Subscription\Service;

use App\Customer\Provider\CustomerProvider;
use App\Subscription\Entity\Subscription;
use App\Subscription\Repository\SubscriptionRepository;
use App\Subscription\Request\SubscriptionRequest;
use App\Vendor\Provider\VendorPlanProvider;
use Ramsey\Uuid\Uuid;

class SubscriptionService
{
    private CustomerProvider $customerProvider;

    private VendorPlanProvider $vendorPlanProvider;

    private SubscriptionRepository $subscriptionRepository;

    public function __construct(
        CustomerProvider $customerProvider,
        VendorPlanProvider $vendorPlanProvider,
        SubscriptionRepository $subscriptionRepository
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->customerProvider = $customerProvider;
        $this->vendorPlanProvider = $vendorPlanProvider;
    }

    public function create(SubscriptionRequest $subscriptionRequest): Subscription
    {
        $customer = $this->customerProvider->get(Uuid::fromString($subscriptionRequest->customerId));
        $vendorPlan = $this->vendorPlanProvider->get(Uuid::fromString($subscriptionRequest->vendorPlanId));

        $subscription = (new Subscription())
            ->setCustomer($customer)
            ->setVendorPlan($vendorPlan)
            ->setExpiresAt((new \DateTime())->add($vendorPlan->getDuration()))
        ;

        $this->subscriptionRepository->save($subscription);

        return $subscription;
    }

    public function delete(Subscription $subscription)
    {
        $this->subscriptionRepository->delete($subscription);
    }
}
