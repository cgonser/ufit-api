<?php

namespace App\Subscription\Provider;

use App\Customer\Entity\Customer;
use App\Subscription\Repository\SubscriptionRepository;

class SubscriptionProvider
{
    private SubscriptionRepository $subscriptionRepository;

    public function __construct(
        SubscriptionRepository $subscriptionRepository
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function findByCustomer(Customer $customer): array
    {
        return $this->subscriptionRepository->findBy([
            'customer' => $customer,
        ]);
    }

    public function findAll(): array
    {
        return $this->subscriptionRepository->findAll();
    }
}