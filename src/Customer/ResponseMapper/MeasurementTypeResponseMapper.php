<?php

declare(strict_types=1);

namespace App\Customer\ResponseMapper;

use App\Customer\Dto\MeasurementTypeDto;
use App\Customer\Entity\MeasurementType;

class MeasurementTypeResponseMapper
{
    public function map(MeasurementType $measurementType): MeasurementTypeDto
    {
        $measurementTypeDto = new MeasurementTypeDto();
        $measurementTypeDto->id = $measurementType->getId()->toString();
        $measurementTypeDto->name = $measurementType->getName();
        $measurementTypeDto->slug = $measurementType->getSlug();
        $measurementTypeDto->units = explode(MeasurementType::UNIT_SEPARATOR, $measurementType->getUnits());

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
