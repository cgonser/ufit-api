<?php

declare(strict_types=1);

namespace App\Customer\Provider;

use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerNotFoundException;
use App\Customer\Repository\CustomerRepository;
use Ramsey\Uuid\UuidInterface;

class CustomerProvider
{
    private CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function get(UuidInterface $customerId): Customer
    {
        /** @var Customer|null $customer */
        $customer = $this->customerRepository->find($customerId);

        if (! $customer) {
            throw new CustomerNotFoundException();
        }

        return $customer;
    }

    public function findOneByEmail(string $emailAddress): ?Customer
    {
        return $this->customerRepository->findOneBy([
            'email' => $emailAddress,
        ]);
    }

    public function findAll(): array
    {
        return $this->customerRepository->findAll();
    }
}
