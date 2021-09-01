<?php

declare(strict_types=1);

namespace App\Customer\Provider;

use App\Customer\Entity\CustomerMeasurement;
use App\Customer\Entity\CustomerMeasurementItem;
use App\Customer\Entity\MeasurementType;
use App\Customer\Exception\CustomerMeasurementItemNotFoundException;
use App\Customer\Repository\CustomerMeasurementItemRepository;
use Ramsey\Uuid\UuidInterface;

class CustomerMeasurementItemProvider
{
    public function __construct(private CustomerMeasurementItemRepository $customerMeasurementItemRepository)
    {
    }

    public function get(UuidInterface $customerMeasurementItemId): CustomerMeasurementItem
    {
        /** @var CustomerMeasurementItem|null $customerMeasurementItem */
        $customerMeasurementItem = $this->customerMeasurementItemRepository->find($customerMeasurementItemId);

        if ($customerMeasurementItem === null) {
            throw new CustomerMeasurementItemNotFoundException();
        }

        return $customerMeasurementItem;
    }

    public function getByCustomerMeasurementAndId(
        CustomerMeasurement $customerMeasurement,
        UuidInterface $customerMeasurementItemId
    ): CustomerMeasurementItem {
        /** @var CustomerMeasurementItem|null $customerMeasurementItem */
        $customerMeasurementItem = $this->customerMeasurementItemRepository->findOneBy([
            'id' => $customerMeasurementItemId,
            'customerMeasurement' => $customerMeasurement,
        ]);

        if ($customerMeasurementItem === null) {
            throw new CustomerMeasurementItemNotFoundException();
        }

        return $customerMeasurementItem;
    }

    /**
     * @return mixed[]
     */
    public function findByCustomerMeasurement(CustomerMeasurement $customerMeasurement): array
    {
        return $this->customerMeasurementItemRepository->findBy([
            'customerMeasurement' => $customerMeasurement,
        ]);
    }

    public function findOneByCustomerMeasurementAndType(
        CustomerMeasurement $customerMeasurement,
        MeasurementType $measurementType
    ): ?object {
        return $this->customerMeasurementItemRepository->findOneBy([
            'customerMeasurement' => $customerMeasurement,
            'measurementType' => $measurementType,
        ]);
    }
}
