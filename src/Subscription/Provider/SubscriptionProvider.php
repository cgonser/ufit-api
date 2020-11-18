<?php

namespace App\Subscription\Provider;

use App\Customer\Entity\Customer;
use App\Subscription\Entity\Subscription;
use App\Subscription\Exception\SubscriptionNotFoundException;
use App\Subscription\Repository\SubscriptionRepository;
use App\Vendor\Entity\Vendor;
use Ramsey\Uuid\UuidInterface;

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

    public function getByCustomerAndId(Customer $customer, UuidInterface $subscriptionId): Subscription
    {
        /** @var Subscription|null $subscription */
        $subscription = $this->subscriptionRepository->findOneBy([
            'id' => $subscriptionId,
            'customer' => $customer,
        ]);

        if (!$subscription) {
            throw new SubscriptionNotFoundException();
        }

        return $subscription;
    }



    public function findAll(): array
    {
        return $this->subscriptionRepository->findAll();
    }
}
