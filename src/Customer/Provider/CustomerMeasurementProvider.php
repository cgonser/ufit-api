<?php

namespace App\Customer\Provider;

use App\Customer\Entity\Customer;
use App\Customer\Entity\CustomerMeasurement;
use App\Customer\Exception\CustomerMeasurementNotFoundException;
use App\Customer\Repository\CustomerMeasurementRepository;
use Ramsey\Uuid\UuidInterface;

class CustomerMeasurementProvider
{
    private CustomerMeasurementRepository $customerMeasurementRepository;

    public function __construct(CustomerMeasurementRepository $customerMeasurementRepository)
    {
        $this->customerMeasurementRepository = $customerMeasurementRepository;
    }

    public function get(UuidInterface $customerMeasurementId): CustomerMeasurement
    {
        /** @var CustomerMeasurement|null $customerMeasurement */
        $customerMeasurement = $this->customerMeasurementRepository->find($customerMeasurementId);

        if (!$customerMeasurement) {
            throw new CustomerMeasurementNotFoundException();
        }

        return $customerMeasurement;
    }

    public function getByCustomerAndId(Customer $customer, UuidInterface $customerMeasurementId): CustomerMeasurement
    {
        /** @var CustomerMeasurement|null $customerMeasurement */
        $customerMeasurement = $this->customerMeasurementRepository->findOneBy([
            'id' => $customerMeasurementId,
            'customer' => $customer,
        ]);

        if (!$customerMeasurement) {
            throw new CustomerMeasurementNotFoundException();
        }

        return $customerMeasurement;
    }

    public function findByCustomer(Customer $customer): array
    {
        return $this->customerMeasurementRepository->findBy(['customer' => $customer]);
    }
}
