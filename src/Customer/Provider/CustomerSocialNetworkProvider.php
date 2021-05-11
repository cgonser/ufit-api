<?php

namespace App\Customer\Provider;

use App\Core\Provider\AbstractProvider;
use App\Customer\Entity\Customer;
use App\Customer\Entity\CustomerSocialNetwork;
use App\Customer\Exception\CustomerSocialNetworkNotFoundException;
use App\Customer\Repository\CustomerSocialNetworkRepository;

class CustomerSocialNetworkProvider extends AbstractProvider
{
    public function __construct(CustomerSocialNetworkRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findOneByCustomerAndPlatform(Customer $customer, string $platform): ?CustomerSocialNetwork
    {
        return $this->repository->findOneBy([
            'customer' => $customer,
            'platform' => $platform,
        ]);
    }

    public function findOneByExternalIdAndPlatform(string $externalId, string $platform): ?CustomerSocialNetwork
    {
        return $this->repository->findOneBy([
            'externalId' => $externalId,
            'platform' => $platform,
        ]);
    }

    public function getByCustomerAndPlatform(Customer $customer, string $platform): ?CustomerSocialNetwork
    {
        $customerSocialNetwork = $this->findOneByCustomerAndPlatform($customer, $platform);

        if (!$customerSocialNetwork) {
            $this->throwNotFoundException();
        }

        return $customerSocialNetwork;
    }

    protected function throwNotFoundException()
    {
        throw new CustomerSocialNetworkNotFoundException();
    }
}
