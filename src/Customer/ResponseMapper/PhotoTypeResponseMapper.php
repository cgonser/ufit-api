<?php

declare(strict_types=1);

namespace App\Customer\ResponseMapper;

use App\Customer\Dto\PhotoTypeDto;
use App\Customer\Entity\PhotoType;

class PhotoTypeResponseMapper
{
    public function map(PhotoType $photoType): PhotoTypeDto
    {
        $photoTypeDto = new PhotoTypeDto();
        $photoTypeDto->id = $photoType->getId()->toString();
        $photoTypeDto->name = $photoType->getName();

        return $photoTypeDto;
    }

    public function mapMultiple(array $photoTypes): array
    {
        $photoTypeDtos = [];

        foreach ($photoTypes as $photoType) {
            $photoTypeDtos[] = $this->map($photoType);
        }

        return $photoTypeDtos;
    }
}
