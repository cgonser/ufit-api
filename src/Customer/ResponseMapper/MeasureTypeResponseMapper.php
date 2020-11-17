<?php

namespace App\Customer\ResponseMapper;

use App\Customer\Dto\MeasureTypeDto;
use App\Customer\Entity\MeasureType;

class MeasureTypeResponseMapper
{
    public function map(MeasureType $measureType): MeasureTypeDto
    {
        $measureTypeDto = new MeasureTypeDto();
        $measureTypeDto->id = $measureType->getId();
        $measureTypeDto->name = $measureType->getName();
        $measureTypeDto->slug = $measureType->getSlug();
        $measureTypeDto->unit = $measureType->getUnit();
        $measureTypeDto->category = $measureType->getCategory();

        return $measureTypeDto;
    }

    public function mapMultiple(array $measureTypes): array
    {
        $measureTypeDtos = [];

        foreach ($measureTypes as $measureType) {
            $measureTypeDtos[] = $this->map($measureType);
        }

        return $measureTypeDtos;
    }
}
