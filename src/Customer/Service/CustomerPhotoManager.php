<?php

namespace App\Customer\Service;

use App\Core\Validation\EntityValidator;
use App\Customer\Entity\CustomerPhoto;
use App\Customer\Exception\CustomerPhotoInvalidPhotoException;
use App\Customer\Repository\CustomerPhotoRepository;
use Intervention\Image\ImageManagerStatic as Image;
use League\Flysystem\FilesystemInterface;

class CustomerPhotoManager
{
    private CustomerPhotoRepository $customerPhotoRepository;

    private EntityValidator $entityValidator;

    private FilesystemInterface $filesystem;

    public function __construct(
        CustomerPhotoRepository $customerPhotoRepository,
        EntityValidator $entityValidator,
        FilesystemInterface $customerPhotoFileSystem
    ) {
        $this->customerPhotoRepository = $customerPhotoRepository;
        $this->entityValidator = $entityValidator;
        $this->filesystem = $customerPhotoFileSystem;
    }

    public function save(CustomerPhoto $customerPhoto): void
    {
        $this->entityValidator->validate($customerPhoto);

        $this->customerPhotoRepository->save($customerPhoto);
    }

    public function delete(CustomerPhoto $customerPhoto): void
    {
        $this->customerPhotoRepository->delete($customerPhoto);
    }

    public function persistPhoto(CustomerPhoto $customerPhoto, string $photoContents)
    {
        $image = Image::make($photoContents);

        if (false === $image) {
            throw new CustomerPhotoInvalidPhotoException();
        }

        $filename = $customerPhoto->getId()->toString().'.png';

        $this->filesystem->put(
            $filename,
            $image->encode('png')
        );

        $customerPhoto->setFilename($filename);

        $this->customerPhotoRepository->save($customerPhoto);
    }

    public function decodePhotoContents(string $photoContents): ?string
    {
        return null !== $photoContents
            ? base64_decode($photoContents)
            : null;
    }
}
