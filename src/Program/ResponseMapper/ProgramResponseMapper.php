<?php

declare(strict_types=1);

namespace App\Program\ResponseMapper;

use App\Program\Dto\ProgramDto;
use App\Program\Entity\Program;
use App\Vendor\ResponseMapper\VendorResponseMapper;

class ProgramResponseMapper
{
    public function __construct(
        private ProgramAssetResponseMapper $programAssetResponseMapper,
        private VendorResponseMapper $vendorResponseMapper,
    ) {
    }

    public function map(Program $program, bool $mapAssets = true, bool $mapVendor = false): ProgramDto
    {
        $programDto = new ProgramDto();
        $programDto->id = $program->getId()->toString();
        $programDto->vendorId = $program->getVendor()->getId()->toString();
        $programDto->name = $program->getName();
        $programDto->level = $program->getLevel();
        $programDto->goals = $program->getGoals();
        $programDto->description = $program->getDescription();
        $programDto->isTemplate = $program->isTemplate();
        $programDto->isActive = $program->isActive();
        $programDto->createdAt = $program->getCreatedAt()?->format(\DateTimeInterface::ATOM);
        $programDto->updatedAt = $program->getUpdatedAt()?->format(\DateTimeInterface::ATOM);

        if ($mapAssets) {
            $programDto->assets = $this->programAssetResponseMapper->mapMultiple($program->getAssets()->toArray());
        }

        if ($mapVendor) {
            $programDto->vendor = $this->vendorResponseMapper->map($program->getVendor());
        }

        return $programDto;
    }

    /**
     * @return ProgramDto[]
     */
    public function mapMultiple(array $programs, bool $mapAssets = false, bool $mapVendor = false): array
    {
        $programDtos = [];

        foreach ($programs as $program) {
            $programDtos[] = $this->map($program, $mapAssets, $mapVendor);
        }

        return $programDtos;
    }
}
