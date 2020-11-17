<?php

namespace App\Customer\Provider;

use App\Customer\Entity\Customer;
use App\Customer\Entity\MeasureType;
use App\Customer\Exception\MeasureTypeNotFoundException;
use App\Customer\Repository\MeasureTypeRepository;
use Ramsey\Uuid\UuidInterface;

class MeasureTypeProvider
{
    private MeasureTypeRepository $measureTypeRepository;

    public function __construct(MeasureTypeRepository $measureTypeRepository)
    {
        $this->measureTypeRepository = $measureTypeRepository;
    }

    public function get(UuidInterface $measureTypeId): MeasureType
    {
        /** @var MeasureType|null $measureType */
        $measureType = $this->measureTypeRepository->find($measureTypeId);

        if (!$measureType) {
            throw new MeasureTypeNotFoundException();
        }

        return $measureType;
    }

    public function getByName(string $name): MeasureType
    {
        /** @var MeasureType|null $measureType */
        $measureType = $this->measureTypeRepository->findOneBy([
            'name' => $name,
        ]);

        if (!$measureType) {
            throw new MeasureTypeNotFoundException();
        }

        return $measureType;
    }

    public function findByCategory(string $category): array
    {
        return $this->measureTypeRepository->findBy(['category' => $category]);
    }

    public function findOneBySlug(string $slug): ?MeasureType
    {
        return $this->measureTypeRepository->findOneBy(['slug' => $slug]);
    }

    public function findOneByCategoryAndName(string $category, string $name): ?MeasureType
    {
        return $this->measureTypeRepository->findOneBy([
            'category' => $category,
            'name' => $name,
        ]);
    }

    public function findAll(): array
    {
        return $this->measureTypeRepository->findAll();
    }
}
