<?php

declare(strict_types=1);

namespace App\Customer\Provider;

use App\Customer\Entity\PhotoType;
use App\Customer\Exception\PhotoTypeNotFoundException;
use App\Customer\Repository\PhotoTypeRepository;
use Ramsey\Uuid\UuidInterface;

class PhotoTypeProvider
{
    public function __construct(private PhotoTypeRepository $photoTypeRepository)
    {
    }

    public function get(UuidInterface $photoTypeId): PhotoType
    {
        /** @var PhotoType|null $photoType */
        $photoType = $this->photoTypeRepository->find($photoTypeId);

        if ($photoType === null) {
            throw new PhotoTypeNotFoundException();
        }

        return $photoType;
    }

    public function getByName(string $name): PhotoType
    {
        /** @var PhotoType|null $photoType */
        $photoType = $this->findOneByName($name);

        if ($photoType === null) {
            throw new PhotoTypeNotFoundException();
        }

        return $photoType;
    }

    public function findOneByName(string $name): ?object
    {
        return $this->photoTypeRepository->findOneBy([
            'name' => $name,
        ]);
    }

    /**
     * @return mixed[]
     */
    public function findAll(): array
    {
        return $this->photoTypeRepository->findAll();
    }
}
