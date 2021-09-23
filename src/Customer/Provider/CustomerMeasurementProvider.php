<?php

declare(strict_types=1);

namespace App\Customer\Provider;

use App\Customer\Entity\Customer;
use App\Customer\Entity\CustomerMeasurement;
use App\Customer\Exception\CustomerMeasurementNotFoundException;
use App\Customer\Repository\CustomerMeasurementRepository;
use Ramsey\Uuid\UuidInterface;

class CustomerMeasurementProvider
{
    public function __construct(private CustomerMeasurementRepository $customerMeasurementRepository)
    {
    }

    public function get(UuidInterface $customerMeasurementId): CustomerMeasurement
    {
        /** @var CustomerMeasurement|null $customerMeasurement */
        $customerMeasurement = $this->customerMeasurementRepository->find($customerMeasurementId);

        if ($customerMeasurement === null) {
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

        if ($customerMeasurement === null) {
            throw new CustomerMeasurementNotFoundException();
        }

        return $customerMeasurement;
    }

    /**
     * @return mixed[]
     */
    public function findByCustomer(Customer $customer): array
    {
        return $this->customerMeasurementRepository->findBy([
            'customer' => $customer,
        ]);
    }
}
