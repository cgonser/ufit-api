<?php

declare(strict_types=1);

namespace App\Customer\Service;

use App\Customer\Entity\PhotoType;
use App\Customer\Exception\PhotoTypeAlreadyExistsException;
use App\Customer\Provider\PhotoTypeProvider;
use App\Customer\Repository\PhotoTypeRepository;
use App\Customer\Request\PhotoTypeRequest;

class PhotoTypeService
{
    private PhotoTypeRepository $photoTypeRepository;

    private PhotoTypeProvider $photoTypeProvider;

    public function __construct(PhotoTypeRepository $photoTypeRepository, PhotoTypeProvider $photoTypeProvider)
    {
        $this->photoTypeRepository = $photoTypeRepository;
        $this->photoTypeProvider = $photoTypeProvider;
    }

    public function create(PhotoTypeRequest $photoTypeRequest): PhotoType
    {
        $photoType = new PhotoType();

        $this->mapFromRequest($photoType, $photoTypeRequest);

        $this->photoTypeRepository->save($photoType);

        return $photoType;
    }

    public function update(PhotoType $photoType, PhotoTypeRequest $photoTypeRequest)
    {
        $this->mapFromRequest($photoType, $photoTypeRequest);

        $this->photoTypeRepository->save($photoType);
    }

    public function mapFromRequest(PhotoType $photoType, PhotoTypeRequest $photoTypeRequest)
    {
        $existingPhotoType = $this->photoTypeProvider->findOneByName($photoTypeRequest->name);

        if ($existingPhotoType &&
            ($photoType->isNew() || $existingPhotoType->getId()->toString() !== $photoType->getId()->toString())
        ) {
            throw new PhotoTypeAlreadyExistsException();
        }

        $photoType->setName($photoTypeRequest->name);
    }

    public function delete(PhotoType $photoType)
    {
        $this->photoTypeRepository->delete($photoType);
    }
}
