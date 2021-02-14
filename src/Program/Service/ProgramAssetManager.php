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

        $this->persistAsset(
            $programAsset,
            $programAssetRequest->filename,
            $this->decodeFileContents($programAssetRequest->contents)
        );

        $this->messageBus->dispatch(new ProgramAssetCreatedEvent($programAsset->getId()));

        return $programAsset;
    }

    public function delete(ProgramAsset $programAsset)
    {
        $this->programAssetRepository->delete($programAsset);

        $this->messageBus->dispatch(new ProgramAssetDeletedEvent($programAsset->getId()));
    }

    private function persistAsset(ProgramAsset $programAsset, string $filename, string $contents)
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $filename = $programAsset->getId()->toString().'.'.$extension;

        $this->filesystem->write($filename, $contents);

        $programAsset->setFilename($filename);

        $this->programAssetRepository->save($programAsset);
    }

    public function decodeFileContents(string $contents): ?string
    {
        return null !== $contents
            ? base64_decode($contents)
            : null;
    }
}
