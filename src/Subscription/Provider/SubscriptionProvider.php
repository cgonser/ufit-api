<?php

namespace App\Subscription\Provider;

use App\Core\Provider\AbstractProvider;
use App\Customer\Entity\Customer;
use App\Subscription\Entity\Subscription;
use App\Subscription\Exception\SubscriptionNotFoundException;
use App\Subscription\Repository\SubscriptionRepository;
use Ramsey\Uuid\UuidInterface;

class SubscriptionProvider extends AbstractProvider
{
    public function __construct(SubscriptionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findByCustomer(Customer $customer): array
    {
        return $this->repository->findBy([
            'customer' => $customer,
        ]);
    }

    public function getByExternalReference(string $externalReference): Subscription
    {
        return $this->getBy([
            'externalReference' => $externalReference,
        ]);
    }

    public function getByCustomerAndId(Customer $customer, UuidInterface $subscriptionId): Subscription
    {
        return $this->getBy([
            'id' => $subscriptionId,
            'customer' => $customer,
        ]);
    }

    protected function throwNotFoundException()
    {
        throw new SubscriptionNotFoundException();
    }
}
