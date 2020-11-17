<?php

namespace App\Customer\Service;

use App\Customer\Entity\MeasureType;
use App\Customer\Exception\MeasureTypeAlreadyExistsException;
use App\Customer\Provider\MeasureTypeProvider;
use App\Customer\Repository\MeasureTypeRepository;
use App\Customer\Request\MeasureTypeRequest;
use Symfony\Component\String\Slugger\SluggerInterface;

class MeasureTypeService
{
    private MeasureTypeRepository $measureTypeRepository;

    private MeasureTypeProvider $measureTypeProvider;

    private SluggerInterface $slugger;

    public function __construct(
        MeasureTypeRepository $measureTypeRepository,
        MeasureTypeProvider $measureTypeProvider,
        SluggerInterface $slugger
    ) {
        $this->measureTypeRepository = $measureTypeRepository;
        $this->measureTypeProvider = $measureTypeProvider;
        $this->slugger = $slugger;
    }

    public function create(MeasureTypeRequest $measureTypeRequest): MeasureType
    {
        $existingMeasureType = $this->measureTypeProvider->findOneByCategoryAndName(
            $measureTypeRequest->category,
            $measureTypeRequest->name
        );

        if ($existingMeasureType) {
            throw new MeasureTypeAlreadyExistsException();
        }

        $measureType = new MeasureType();

        $this->mapFromRequest($measureType, $measureTypeRequest);

        $this->measureTypeRepository->save($measureType);

        return $measureType;
    }

    public function update(MeasureType $measureType, MeasureTypeRequest $measureTypeRequest)
    {
        $this->mapFromRequest($measureType, $measureTypeRequest);

        $this->measureTypeRepository->save($measureType);
    }

    public function mapFromRequest(MeasureType $measureType, MeasureTypeRequest $measureTypeRequest)
    {
        $existingMeasureType = $this->measureTypeProvider->findOneByCategoryAndName(
            $measureTypeRequest->category,
            $measureTypeRequest->name
        );

        if ($existingMeasureType &&
            ($measureType->isNew() || $existingMeasureType->getId()->toString() != $measureType->getId()->toString())
        ) {
            throw new MeasureTypeAlreadyExistsException();
        }

        $measureType->setName($measureTypeRequest->name);
        $measureType->setUnit($measureTypeRequest->unit);
        $measureType->setCategory($measureTypeRequest->category);
        $measureType->setSlug($this->generateSlug($measureType));
    }

    public function delete(MeasureType $measureType)
    {
        $this->measureTypeRepository->delete($measureType);
    }

    public function generateSlug(MeasureType $measureType, ?int $suffix = null): string
    {
        $slug = strtolower($this->slugger->slug($measureType->getName()));

        if (null !== $suffix) {
            $slug .= '-'.(string) $suffix;
        }

        if ($this->isSlugUnique($measureType, $slug)) {
            return $slug;
        }

        $suffix = null !== $suffix ? $suffix + 1 : 1;

        return $this->generateSlug($measureType, $suffix);
    }

    private function isSlugUnique(MeasureType $measureType, string $slug): bool
    {
        $existingMeasureType = $this->measureTypeProvider->findOneBySlug($slug);

        if (!$existingMeasureType) {
            return true;
        }

        if (!$measureType->isNew() && $existingMeasureType->getId()->toString() == $measureType->getId()->toString()) {
            return true;
        }

        return false;
    }
}
