<?php

namespace App\Customer\ResponseMapper;

use App\Customer\Dto\CustomerPhotoDto;
use App\Customer\Entity\CustomerPhoto;

class CustomerPhotoResponseMapper
{
    public function map(CustomerPhoto $customerPhoto): CustomerPhotoDto
    {
        $customerPhotoDto = new CustomerPhotoDto();
        $customerPhotoDto->id = $customerPhoto->getId()->toString();
        $customerPhotoDto->customerId = $customerPhoto->getCustomer()->getId()->toString();
        $customerPhotoDto->title = $customerPhoto->getTitle() ?? '';
        $customerPhotoDto->description = $customerPhoto->getDescription() ?? '';
        $customerPhotoDto->takenAt = $customerPhoto->getTakenAt()->format(\DateTimeInterface::ISO8601);

        return $customerPhotoDto;
    }

    public function mapMultiple(array $customerPhotos): array
    {
        $customerPhotoDTOs = [];

        foreach ($customerPhotos as $customerPhoto) {
            $customerPhotoDTOs[] = $this->map($customerPhoto);
        }

        return $customerPhotoDTOs;
    }
}
