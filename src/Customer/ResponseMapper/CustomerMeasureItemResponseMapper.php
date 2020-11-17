<?php

namespace App\Customer\ResponseMapper;

use App\Customer\Dto\CustomerMeasureItemDto;
use App\Customer\Entity\CustomerMeasureItem;

class CustomerMeasureItemResponseMapper
{
    public function map(CustomerMeasureItem $customerMeasureItem): CustomerMeasureItemDto
    {
        $customerMeasureItemDto = new CustomerMeasureItemDto();
        $customerMeasureItemDto->id = $customerMeasureItem->getId()->toString();
        $customerMeasureItemDto->customerMeasureId = $customerMeasureItem->getCustomerMeasure()->getId()->toString();
        $customerMeasureItemDto->type = $customerMeasureItem->getMeasureType()->getName();
        $customerMeasureItemDto->measure = (string) $customerMeasureItem->getMeasure();
        $customerMeasureItemDto->unit = $customerMeasureItem->getMeasureType()->getUnit();

        return $customerMeasureItemDto;
    }

    public function mapMultiple(array $customerMeasureItems): array
    {
        $customerMeasureItemDtos = [];

        foreach ($customerMeasureItems as $customerMeasureItem) {
            $customerMeasureItemDtos[] = $this->map($customerMeasureItem);
        }

        return $customerMeasureItemDtos;
    }
}
