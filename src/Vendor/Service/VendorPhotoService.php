<?php

namespace App\Vendor\Service;

use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\VendorInvalidPhotoException;
use App\Vendor\Repository\VendorRepository;
use App\Vendor\Request\VendorPhotoRequest;
use GuzzleHttp\Client;
use League\Flysystem\FilesystemInterface;

class VendorPhotoService
{
    private VendorRepository $vendorRepository;

    private FilesystemInterface $filesystem;

    public function __construct(
        VendorRepository $vendorRepository,
        FilesystemInterface $vendorPhotoFileSystem
    ) {
        $this->vendorRepository = $vendorRepository;
        $this->filesystem = $vendorPhotoFileSystem;
    }

    public function uploadFromRequest(Vendor $vendor, VendorPhotoRequest $vendorPhotoRequest)
    {
        $this->uploadPhoto(
            $vendor,
            $this->decodePhotoContents($vendorPhotoRequest->photoContents)
        );
    }

    public function uploadPhoto(Vendor $vendor, string $photoContents)
    {
        $imageInfo = getimagesizefromstring($photoContents);

        if (false === $imageInfo) {
            throw new VendorInvalidPhotoException();
        }

        $extension = explode('/', $imageInfo['mime'])[1];

        $filename = $vendor->getId()->toString().'.'.$extension;

        $this->filesystem->put($filename, $photoContents, ['ACL' => 'public-read']);

        $vendor->setPhoto($filename);

        $this->vendorRepository->save($vendor);
    }

    public function decodePhotoContents(string $photoContents): ?string
    {
        return null !== $photoContents
            ? base64_decode($photoContents)
            : null;
    }

    public function uploadFromUrl(Vendor $vendor, string $photoUrl): bool
    {
        try {
            $httpClient = new Client();
            $photo = $httpClient->get($photoUrl);

            $photoContents = $photo->getBody()->getContents();

            $this->uploadPhoto($vendor, $photoContents);

            return true;
        } catch (\Exception $e) {
            echo $e;
            return false;
        }
    }
}