<?php

namespace App\Customer\Provider;

use App\Customer\Entity\Customer;
use App\Customer\Entity\MeasurementType;
use App\Customer\Exception\MeasurementTypeNotFoundException;
use App\Customer\Repository\MeasurementTypeRepository;
use Ramsey\Uuid\UuidInterface;

class MeasurementTypeProvider
{
    private MeasurementTypeRepository $measurementTypeRepository;

    public function __construct(MeasurementTypeRepository $measurementTypeRepository)
    {
        $this->measurementTypeRepository = $measurementTypeRepository;
    }

    public function get(UuidInterface $measurementTypeId): MeasurementType
    {
        /** @var MeasurementType|null $measurementType */
        $measurementType = $this->measurementTypeRepository->find($measurementTypeId);

        if (!$measurementType) {
            throw new MeasurementTypeNotFoundException();
        }

        return $measurementType;
    }

    public function getByName(string $name): MeasurementType
    {
        /** @var MeasurementType|null $measurementType */
        $measurementType = $this->measurementTypeRepository->findOneBy([
            'name' => $name,
        ]);

        if (!$measurementType) {
            throw new MeasurementTypeNotFoundException();
        }

        return $measurementType;
    }

    public function getBySlug(string $slug): MeasurementType
    {
        /** @var MeasurementType|null $measurementType */
        $measurementType = $this->findOneBySlug($slug);

        if (!$measurementType) {
            throw new MeasurementTypeNotFoundException();
        }

        return $measurementType;
    }

    public function findOneBySlug(string $slug): ?MeasurementType
    {
        return $this->measurementTypeRepository->findOneBy(['slug' => $slug]);
    }

    public function findOneByName(string $name): ?MeasurementType
    {
        return $this->measurementTypeRepository->findOneBy(['name' => $name]);
    }

    public function findAll(): array
    {
        return $this->measurementTypeRepository->findAll();
    }
}
