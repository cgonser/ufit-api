<?php

declare(strict_types=1);

namespace App\Subscription\Provider;

use App\Core\Provider\AbstractProvider;
use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerNotFoundException;
use App\Subscription\Repository\SubscriptionRepository;
use App\Vendor\Entity\Vendor;
use Ramsey\Uuid\UuidInterface;

class SubscriptionCustomerProvider extends AbstractProvider
{
    public function __construct(SubscriptionRepository $subscriptionRepository)
    {
        $this->repository = $subscriptionRepository;
    }

    public function getVendorCustomer(Vendor $vendor, UuidInterface $customerId): Customer
    {
        $customer = $this->repository->findOneVendorCustomer($vendor, $customerId);

        if (null === $customer) {
            throw new CustomerNotFoundException();
        }

        return $customer;
    }

    protected function throwNotFoundException(): void
    {
        throw new CustomerNotFoundException();
    }

    protected function getFilterableFields(): array
    {
        return ['vendorPlanId', 'customerId', 'vendorId', 'isActive'];
    }
}
