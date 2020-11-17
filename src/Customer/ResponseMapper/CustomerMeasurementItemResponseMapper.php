<?php

namespace App\Customer\ResponseMapper;

use App\Customer\Dto\CustomerMeasurementItemDto;
use App\Customer\Entity\CustomerMeasurementItem;

class CustomerMeasurementItemResponseMapper
{
    public function map(CustomerMeasurementItem $customerMeasurementItem): CustomerMeasurementItemDto
    {
        $customerMeasurementItemDto = new CustomerMeasurementItemDto();
        $customerMeasurementItemDto->id = $customerMeasurementItem->getId()->toString();
        $customerMeasurementItemDto->customerMeasurementId = $customerMeasurementItem->getCustomerMeasurement()->getId()->toString();
        $customerMeasurementItemDto->type = $customerMeasurementItem->getMeasurementType()->getName();
        $customerMeasurementItemDto->measurement = (string) $customerMeasurementItem->getMeasurement();
        $customerMeasurementItemDto->unit = $customerMeasurementItem->getMeasurementType()->getUnit();

        return $customerMeasurementItemDto;
    }

    public function mapMultiple(array $customerMeasurementItems): array
    {
        $customerMeasurementItemDtos = [];

        foreach ($customerMeasurementItems as $customerMeasurementItem) {
            $customerMeasurementItemDtos[] = $this->map($customerMeasurementItem);
        }

        return $customerMeasurementItemDtos;
    }
}
