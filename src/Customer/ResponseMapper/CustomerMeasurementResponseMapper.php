<?php

namespace App\Customer\ResponseMapper;

use App\Customer\Dto\CustomerMeasurementDto;
use App\Customer\Entity\CustomerMeasurement;

class CustomerMeasurementResponseMapper
{
    private CustomerMeasurementItemResponseMapper $customerMeasurementItemResponseMapper;

    public function __construct(CustomerMeasurementItemResponseMapper $customerMeasurementItemResponseMapper)
    {
        $this->customerMeasurementItemResponseMapper = $customerMeasurementItemResponseMapper;
    }

    public function map(CustomerMeasurement $customerMeasurement): CustomerMeasurementDto
    {
        $customerMeasurementDto = new CustomerMeasurementDto();
        $customerMeasurementDto->id = $customerMeasurement->getId()->toString();
        $customerMeasurementDto->notes = $customerMeasurement->getNotes() ?? '';
        $customerMeasurementDto->takenAt = $customerMeasurement->getTakenAt()->format(\DateTimeInterface::ISO8601);
        $customerMeasurementDto->items = $this->customerMeasurementItemResponseMapper->mapMultiple(
            $customerMeasurement->getItems()->toArray()
        );

        return $customerMeasurementDto;
    }

    public function mapMultiple(array $customerMeasurements): array
    {
        $customerMeasurementDtos = [];

        foreach ($customerMeasurements as $customerMeasurement) {
            $customerMeasurementDtos[] = $this->map($customerMeasurement);
        }

        return $customerMeasurementDtos;
    }
}
