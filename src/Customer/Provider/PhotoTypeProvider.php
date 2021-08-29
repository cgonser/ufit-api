<?php

declare(strict_types=1);

namespace App\Customer\Provider;

use App\Customer\Entity\PhotoType;
use App\Customer\Exception\PhotoTypeNotFoundException;
use App\Customer\Repository\PhotoTypeRepository;
use Ramsey\Uuid\UuidInterface;

class PhotoTypeProvider
{
    private PhotoTypeRepository $photoTypeRepository;

    public function __construct(PhotoTypeRepository $photoTypeRepository)
    {
        $this->photoTypeRepository = $photoTypeRepository;
    }

    public function get(UuidInterface $photoTypeId): PhotoType
    {
        /** @var PhotoType|null $photoType */
        $photoType = $this->photoTypeRepository->find($photoTypeId);

        if (! $photoType) {
            throw new PhotoTypeNotFoundException();
        }

        return $photoType;
    }

    public function getByName(string $name): PhotoType
    {
        /** @var PhotoType|null $photoType */
        $photoType = $this->findOneByName($name);

        if (! $photoType) {
            throw new PhotoTypeNotFoundException();
        }

        return $photoType;
    }

    public function findOneByName(string $name): ?PhotoType
    {
        return $this->photoTypeRepository->findOneBy([
            'name' => $name,
        ]);
    }

    public function findAll(): array
    {
        return $this->photoTypeRepository->findAll();
    }
}
