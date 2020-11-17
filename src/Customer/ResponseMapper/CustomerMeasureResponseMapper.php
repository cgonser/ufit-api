<?php

namespace App\Customer\ResponseMapper;

use App\Customer\Dto\CustomerMeasureDto;
use App\Customer\Entity\CustomerMeasure;

class CustomerMeasureResponseMapper
{
    private CustomerMeasureItemResponseMapper $customerMeasureItemResponseMapper;

    public function __construct(CustomerMeasureItemResponseMapper $customerMeasureItemResponseMapper)
    {
        $this->customerMeasureItemResponseMapper = $customerMeasureItemResponseMapper;
    }

    public function map(CustomerMeasure $customerMeasure): CustomerMeasureDto
    {
        $customerMeasureDto = new CustomerMeasureDto();
        $customerMeasureDto->id = $customerMeasure->getId()->toString();
        $customerMeasureDto->notes = $customerMeasure->getNotes() ?? '';
        $customerMeasureDto->takenAt = $customerMeasure->getTakenAt()->format(\DateTimeInterface::ISO8601);
        $customerMeasureDto->items = $this->customerMeasureItemResponseMapper->mapMultiple(
            $customerMeasure->getItems()->toArray()
        );

        return $customerMeasureDto;
    }

    public function mapMultiple(array $customerMeasures): array
    {
        $customerMeasureDtos = [];

        foreach ($customerMeasures as $customerMeasure) {
            $customerMeasureDtos[] = $this->map($customerMeasure);
        }

        return $customerMeasureDtos;
    }
}
