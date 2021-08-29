<?php

declare(strict_types=1);

namespace App\Customer\Service;

use App\Customer\Entity\MeasurementType;
use App\Customer\Exception\MeasurementTypeAlreadyExistsException;
use App\Customer\Provider\MeasurementTypeProvider;
use App\Customer\Repository\MeasurementTypeRepository;
use App\Customer\Request\MeasurementTypeRequest;
use Symfony\Component\String\Slugger\SluggerInterface;

class MeasurementTypeService
{
    private MeasurementTypeRepository $measurementTypeRepository;

    private MeasurementTypeProvider $measurementTypeProvider;

    private SluggerInterface $slugger;

    public function __construct(
        MeasurementTypeRepository $measurementTypeRepository,
        MeasurementTypeProvider $measurementTypeProvider,
        SluggerInterface $slugger
    ) {
        $this->measurementTypeRepository = $measurementTypeRepository;
        $this->measurementTypeProvider = $measurementTypeProvider;
        $this->slugger = $slugger;
    }

    public function create(MeasurementTypeRequest $measurementTypeRequest): MeasurementType
    {
        $measurementType = new MeasurementType();

        $this->mapFromRequest($measurementType, $measurementTypeRequest);

        $this->measurementTypeRepository->save($measurementType);

        return $measurementType;
    }

    public function update(MeasurementType $measurementType, MeasurementTypeRequest $measurementTypeRequest)
    {
        $this->mapFromRequest($measurementType, $measurementTypeRequest);

        $this->measurementTypeRepository->save($measurementType);
    }

    public function mapFromRequest(MeasurementType $measurementType, MeasurementTypeRequest $measurementTypeRequest)
    {
        $existingMeasurementType = $this->measurementTypeProvider->findOneByName($measurementTypeRequest->name);

        if ($existingMeasurementType &&
            ($measurementType->isNew() || $existingMeasurementType->getId()->toString() !== $measurementType->getId()->toString())
        ) {
            throw new MeasurementTypeAlreadyExistsException();
        }

        $measurementType->setName($measurementTypeRequest->name);
        $measurementType->setUnits(implode(MeasurementType::UNIT_SEPARATOR, $measurementTypeRequest->units));
        $measurementType->setSlug($this->generateSlug($measurementType));
    }

    public function delete(MeasurementType $measurementType)
    {
        $this->measurementTypeRepository->delete($measurementType);
    }

    public function generateSlug(MeasurementType $measurementType, ?int $suffix = null): string
    {
        $slug = strtolower($this->slugger->slug($measurementType->getName())->toString());

        if (null !== $suffix) {
            $slug .= '-'.(string) $suffix;
        }

        if ($this->isSlugUnique($measurementType, $slug)) {
            return $slug;
        }

        $suffix = null !== $suffix ? $suffix + 1 : 1;

        return $this->generateSlug($measurementType, $suffix);
    }

    private function isSlugUnique(MeasurementType $measurementType, string $slug): bool
    {
        $existingMeasurementType = $this->measurementTypeProvider->findOneBySlug($slug);

        if (! $existingMeasurementType) {
            return true;
        }

        if (! $measurementType->isNew() && $existingMeasurementType->getId()->toString() === $measurementType->getId()->toString()) {
            return true;
        }

        return false;
    }
}
