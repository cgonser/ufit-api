<?php

declare(strict_types=1);

namespace App\Customer\ResponseMapper;

use App\Customer\Dto\CustomerMeasurementItemDto;
use App\Customer\Entity\CustomerMeasurementItem;

class CustomerMeasurementItemResponseMapper
{
    private MeasurementTypeResponseMapper $measurementTypeResponseMapper;

    public function __construct(MeasurementTypeResponseMapper $measurementTypeResponseMapper)
    {
        $this->measurementTypeResponseMapper = $measurementTypeResponseMapper;
    }

    public function map(CustomerMeasurementItem $customerMeasurementItem): CustomerMeasurementItemDto
    {
        $customerMeasurementItemDto = new CustomerMeasurementItemDto();
        $customerMeasurementItemDto->id = $customerMeasurementItem->getId()
            ->toString();
        $customerMeasurementItemDto->measurement = $customerMeasurementItem->getMeasurement()
            ->toFloat();
        $customerMeasurementItemDto->unit = $customerMeasurementItem->getUnit();
        $customerMeasurementItemDto->measurementType = $this->measurementTypeResponseMapper->map(
            $customerMeasurementItem->getMeasurementType()
        );

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
