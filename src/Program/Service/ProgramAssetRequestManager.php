<?php

namespace App\Program\Service;

use App\Program\Entity\Program;
use App\Program\Entity\ProgramAsset;
use App\Program\Request\ProgramAssetRequest;

class ProgramAssetRequestManager
{
    private ProgramAssetManager $programAssetManager;

    public function __construct(
        ProgramAssetManager $programAssetManager
    ) {
        $this->programAssetManager = $programAssetManager;
    }

    public function createFromRequest(Program $program, ProgramAssetRequest $programAssetRequest): ProgramAsset
    {
        $programAsset = new ProgramAsset();
        $programAsset->setProgram($program);

        $this->mapFromRequest($programAsset, $programAssetRequest);

        $this->programAssetManager->save($programAsset);

        return $programAsset;
    }

    public function updateFromRequest(ProgramAsset $programAsset, ProgramAssetRequest $programAssetRequest): void
    {
        $this->mapFromRequest($programAsset, $programAssetRequest);

        $this->programAssetManager->save($programAsset);
    }

    public function mapFromRequest(ProgramAsset $programAsset, ProgramAssetRequest $programAssetRequest): void
    {
        if (null !== $programAssetRequest->title) {
            $programAsset->setTitle($programAssetRequest->title);
        }

        if (null !== $programAssetRequest->type) {
            $programAsset->setType($programAssetRequest->type);
        }
    }
}