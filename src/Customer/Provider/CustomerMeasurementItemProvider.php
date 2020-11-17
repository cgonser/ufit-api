<?php

namespace App\Customer\Provider;

use App\Customer\Entity\CustomerMeasurement;
use App\Customer\Entity\CustomerMeasurementItem;
use App\Customer\Entity\MeasurementType;
use App\Customer\Exception\CustomerMeasurementItemNotFoundException;
use App\Customer\Repository\CustomerMeasurementItemRepository;
use Ramsey\Uuid\UuidInterface;

class CustomerMeasurementItemProvider
{
    private CustomerMeasurementItemRepository $customerMeasurementItemRepository;

    public function __construct(
        CustomerMeasurementItemRepository $customerMeasurementItemRepository
    ) {
        $this->customerMeasurementItemRepository = $customerMeasurementItemRepository;
    }

    public function get(UuidInterface $customerMeasurementItemId): CustomerMeasurementItem
    {
        /** @var CustomerMeasurementItem|null $customerMeasurementItem */
        $customerMeasurementItem = $this->customerMeasurementItemRepository->find($customerMeasurementItemId);

        if (!$customerMeasurementItem) {
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

        if (!$customerMeasurementItem) {
            throw new CustomerMeasurementItemNotFoundException();
        }

        return $customerMeasurementItem;
    }

    public function findByCustomerMeasurement(CustomerMeasurement $customerMeasurement): array
    {
        return $this->customerMeasurementItemRepository->findBy(['customerMeasurement' => $customerMeasurement]);
    }

    public function findOneByCustomerMeasurementAndType(
        CustomerMeasurement $customerMeasurement,
        MeasurementType $measurementType
    ): ?CustomerMeasurementItem {
        return $this->customerMeasurementItemRepository->findOneBy([
            'customerMeasurement' => $customerMeasurement,
            'measurementType' => $measurementType,
        ]);
    }
}
