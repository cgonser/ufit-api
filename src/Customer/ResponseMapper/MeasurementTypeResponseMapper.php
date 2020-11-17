<?php

namespace App\Customer\ResponseMapper;

use App\Customer\Dto\MeasurementTypeDto;
use App\Customer\Entity\MeasurementType;

class MeasurementTypeResponseMapper
{
    public function map(MeasurementType $measurementType): MeasurementTypeDto
    {
        $measurementTypeDto = new MeasurementTypeDto();
        $measurementTypeDto->id = $measurementType->getId();
        $measurementTypeDto->name = $measurementType->getName();
        $measurementTypeDto->slug = $measurementType->getSlug();
        $measurementTypeDto->unit = $measurementType->getUnit();
        $measurementTypeDto->category = $measurementType->getCategory();

        return $measurementTypeDto;
    }

    public function mapMultiple(array $measurementTypes): array
    {
        $measurementTypeDtos = [];

        foreach ($measurementTypes as $measurementType) {
            $measurementTypeDtos[] = $this->map($measurementType);
        }

        return $measurementTypeDtos;
    }
}
