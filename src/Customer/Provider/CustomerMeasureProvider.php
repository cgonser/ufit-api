<?php

namespace App\Customer\Provider;

use App\Customer\Entity\Customer;
use App\Customer\Entity\CustomerMeasure;
use App\Customer\Exception\CustomerMeasureNotFoundException;
use App\Customer\Repository\CustomerMeasureRepository;
use Ramsey\Uuid\UuidInterface;

class CustomerMeasureProvider
{
    private CustomerMeasureRepository $customerMeasureRepository;

    public function __construct(CustomerMeasureRepository $customerMeasureRepository)
    {
        $this->customerMeasureRepository = $customerMeasureRepository;
    }

    public function get(UuidInterface $customerMeasureId): CustomerMeasure
    {
        /** @var CustomerMeasure|null $customerMeasure */
        $customerMeasure = $this->customerMeasureRepository->find($customerMeasureId);

        if (!$customerMeasure) {
            throw new CustomerMeasureNotFoundException();
        }

        return $customerMeasure;
    }

    public function getByCustomerAndId(Customer $customer, UuidInterface $customerMeasureId): CustomerMeasure
    {
        /** @var CustomerMeasure|null $customerMeasure */
        $customerMeasure = $this->customerMeasureRepository->findOneBy([
            'id' => $customerMeasureId,
            'customer' => $customer,
        ]);

        if (!$customerMeasure) {
            throw new CustomerMeasureNotFoundException();
        }

        return $customerMeasure;
    }

    public function findByCustomer(Customer $customer): array
    {
        return $this->customerMeasureRepository->findBy(['customer' => $customer]);
    }
}
