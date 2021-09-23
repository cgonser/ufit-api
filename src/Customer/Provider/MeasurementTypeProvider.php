<?php

declare(strict_types=1);

namespace App\Customer\Provider;

use App\Customer\Entity\MeasurementType;
use App\Customer\Exception\MeasurementTypeNotFoundException;
use App\Customer\Repository\MeasurementTypeRepository;
use Ramsey\Uuid\UuidInterface;

class MeasurementTypeProvider
{
    public function __construct(private MeasurementTypeRepository $measurementTypeRepository)
    {
    }

    public function get(UuidInterface $measurementTypeId): MeasurementType
    {
        /** @var MeasurementType|null $measurementType */
        $measurementType = $this->measurementTypeRepository->find($measurementTypeId);

        if ($measurementType === null) {
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

        if ($measurementType === null) {
            throw new MeasurementTypeNotFoundException();
        }

        return $measurementType;
    }

    public function getBySlug(string $slug): MeasurementType
    {
        /** @var MeasurementType|null $measurementType */
        $measurementType = $this->findOneBySlug($slug);

        if ($measurementType === null) {
            throw new MeasurementTypeNotFoundException();
        }

        return $measurementType;
    }

    public function findOneBySlug(string $slug): ?object
    {
        return $this->measurementTypeRepository->findOneBy([
            'slug' => $slug,
        ]);
    }

    public function findOneByName(string $name): ?object
    {
        return $this->measurementTypeRepository->findOneBy([
            'name' => $name,
        ]);
    }

    /**
     * @return mixed[]
     */
    public function findAll(): array
    {
        return $this->measurementTypeRepository->findAll();
    }
}
