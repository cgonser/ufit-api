<?php

namespace App\Program\Service;

use App\Program\Entity\Program;
use App\Program\Entity\ProgramAsset;
use App\Program\Message\ProgramAssetCreatedEvent;
use App\Program\Message\ProgramAssetDeletedEvent;
use App\Program\Repository\ProgramAssetRepository;
use App\Program\Request\ProgramAssetRequest;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ProgramAssetManager
{
    private ProgramAssetRepository $programAssetRepository;

    private FilesystemInterface $filesystem;

    private MessageBusInterface $messageBus;

    public function __construct(
        ProgramAssetRepository $programAssetRepository,
        FilesystemInterface $programAssetFileSystem,
        MessageBusInterface $messageBus
    ) {
        $this->programAssetRepository = $programAssetRepository;
        $this->filesystem = $programAssetFileSystem;
        $this->messageBus = $messageBus;
    }

    public function createFromRequest(Program $program, ProgramAssetRequest $programAssetRequest): ProgramAsset
    {
        $programAsset = new ProgramAsset();
        $programAsset->setProgram($program);

        if (null !== $programAssetRequest->title) {
            $programAsset->setTitle($programAssetRequest->title);
        }

        if (null !== $programAssetRequest->type) {
            $programAsset->setType($programAssetRequest->type);
        }

        $this->programAssetRepository->save($programAsset);

        $this->messageBus->dispatch(new ProgramAssetCreatedEvent($programAsset->getId()));

        return $programAsset;
    }

    public function delete(ProgramAsset $programAsset): void
    {
        $this->programAssetRepository->delete($programAsset);

        $this->messageBus->dispatch(new ProgramAssetDeletedEvent($programAsset->getId()));
    }

    public function uploadAsset(ProgramAsset $programAsset, string $contents): void
    {
        $extension = pathinfo($programAsset->getFilename(), PATHINFO_EXTENSION);
        $filename = $programAsset->getId()->toString().'.'.$extension;

        $this->filesystem->put($filename, $contents);

        $programAsset->setFilename($filename);

        $this->programAssetRepository->save($programAsset);
    }
}
