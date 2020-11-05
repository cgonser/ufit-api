<?php

namespace App\Marketplace\Service\Subscription;

use App\Marketplace\Entity\Subscription;
use App\Marketplace\Repository\SubscriptionRepository;
use App\Customer\Entity\Customer;
use App\Vendor\Entity\VendorPlan;

class SubscriptionManager
{
    private SubscriptionRepository $subscriptionRepository;

    public function __construct(SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function createSubscription(Customer $customer, VendorPlan $vendorPlan)
    {
        $subscription = (new Subscription())
            ->setCustomer($customer)
            ->setVendorPlan($vendorPlan)
            ->setExpiresAt((new \DateTime)->add($vendorPlan->getDuration()))
        ;

        $this->subscriptionRepository->save($subscription);
    }
}