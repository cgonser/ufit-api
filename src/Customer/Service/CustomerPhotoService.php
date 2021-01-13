<?php

namespace App\Customer\Service;

use App\Customer\Entity\Customer;
use App\Customer\Entity\CustomerPhoto;
use App\Customer\Exception\CustomerPhotoInvalidPhotoException;
use App\Customer\Exception\CustomerPhotoInvalidTakenAtException;
use App\Customer\Provider\CustomerPhotoProvider;
use App\Customer\Repository\CustomerPhotoRepository;
use App\Customer\Request\CustomerPhotoRequest;
use League\Flysystem\FilesystemInterface;

class CustomerPhotoService
{
    private CustomerPhotoRepository $customerPhotoRepository;

    private CustomerPhotoProvider $customerPhotoProvider;

    private FilesystemInterface $filesystem;

    public function __construct(
        CustomerPhotoRepository $customerPhotoRepository,
        CustomerPhotoProvider $customerPhotoProvider,
        FilesystemInterface $customerPhotoFileSystem
    ) {
        $this->customerPhotoRepository = $customerPhotoRepository;
        $this->customerPhotoProvider = $customerPhotoProvider;
        $this->filesystem = $customerPhotoFileSystem;
    }

    public function create(Customer $customer, CustomerPhotoRequest $customerPhotoRequest): CustomerPhoto
    {
        $customerPhoto = new CustomerPhoto();
        $customerPhoto->setCustomer($customer);

        $this->mapFromRequest($customerPhoto, $customerPhotoRequest);

        $this->customerPhotoRepository->save($customerPhoto);

        if (null !== $customerPhotoRequest->photoContents) {
            $this->persistPhoto(
                $customerPhoto,
                $this->decodePhotoContents($customerPhotoRequest->photoContents)
            );
        }

        return $customerPhoto;
    }

    public function update(CustomerPhoto $customerPhoto, CustomerPhotoRequest $customerPhotoRequest)
    {
        $this->mapFromRequest($customerPhoto, $customerPhotoRequest);

        $this->customerPhotoRepository->save($customerPhoto);

        if (null !== $customerPhotoRequest->photoContents) {
            $this->persistPhoto(
                $customerPhoto,
                $this->decodePhotoContents($customerPhotoRequest->photoContents)
            );
        }
    }

    public function delete(CustomerPhoto $customerPhoto)
    {
        $this->customerPhotoRepository->delete($customerPhoto);
    }

    private function mapFromRequest(CustomerPhoto $customerPhoto, CustomerPhotoRequest $customerPhotoRequest)
    {
        // TODO: photoType
        $customerPhoto->setTitle($customerPhotoRequest->title);
        $customerPhoto->setDescription($customerPhotoRequest->description);

        if (null !== $customerPhotoRequest->takenAt) {
            $takenAt = \DateTime::createFromFormat($customerPhotoRequest->takenAt, \DateTimeInterface::ISO8601);

            if (false === $takenAt) {
                throw new CustomerPhotoInvalidTakenAtException();
            }

            $customerPhoto->setTakenAt($takenAt);
        } elseif (null === $customerPhoto->getTakenAt()) {
            $customerPhoto->setTakenAt(new \DateTime());
        }
    }

    private function persistPhoto(CustomerPhoto $customerPhoto, string $photoContents)
    {
        $imageInfo = getimagesizefromstring($photoContents);

        if (false === $imageInfo) {
            throw new CustomerPhotoInvalidPhotoException();
        }

        $extension = explode('/', $imageInfo['mime'])[1];

        $filename = $customerPhoto->getId()->toString().'.'.$extension;

        $this->filesystem->write($filename, $photoContents);

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
