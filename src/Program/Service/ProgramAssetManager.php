<?php

declare(strict_types=1);

namespace App\Program\Service;

use App\Program\Entity\ProgramAsset;
use App\Program\Message\ProgramAssetCreatedEvent;
use App\Program\Message\ProgramAssetDeletedEvent;
use App\Program\Repository\ProgramAssetRepository;
use League\Flysystem\FilesystemInterface;
use Mimey\MimeTypes;
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

    public function create(ProgramAsset $programAsset): void
    {
        $this->save($programAsset);

        $this->messageBus->dispatch(new ProgramAssetCreatedEvent($programAsset->getId()));
    }

    public function save(ProgramAsset $programAsset): void
    {
        $this->programAssetRepository->save($programAsset);
    }

    public function delete(ProgramAsset $programAsset): void
    {
        $this->programAssetRepository->delete($programAsset);

        $this->messageBus->dispatch(new ProgramAssetDeletedEvent($programAsset->getId()));
    }

    public function uploadAsset(ProgramAsset $programAsset, string $contents, ?string $type = null): void
    {
        $extension = null;

        if (null !== $type) {
            $mimeTypes = new MimeTypes();
            $extension = $mimeTypes->getExtension($type);
        }

        if (null === $extension && null !== $programAsset->getFilename()) {
            $extension = pathinfo($programAsset->getFilename(), PATHINFO_EXTENSION);
        }

        $filename = $programAsset->getId()
            ->toString().(null !== $extension ? ('.'.$extension) : '');

        $this->filesystem->put($filename, $contents);

        $programAsset->setFilename($filename);
        $programAsset->setType($type);

        $this->save($programAsset);
    }
}
