<?php

namespace App\Vendor\Service;

use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\VendorInvalidPhotoException;
use Intervention\Image\ImageManagerStatic as Image;
use GuzzleHttp\Client;
use League\Flysystem\FilesystemInterface;

class VendorPhotoManager
{
    private VendorManager $vendorManager;
    private FilesystemInterface $filesystem;

    public function __construct(
        VendorManager $vendorManager,
        FilesystemInterface $vendorPhotoFileSystem
    ) {
        $this->vendorManager = $vendorManager;
        $this->filesystem = $vendorPhotoFileSystem;
    }

    public function uploadPhoto(Vendor $vendor, string $photoContents): void
    {
        $image = Image::make($photoContents);

        if (false === $image) {
            throw new VendorInvalidPhotoException();
        }

        $filename = $vendor->getId()->toString().'.png';

        $this->filesystem->put(
            $filename,
            $image->encode('png'),
            ['ACL' => 'public-read']
        );

        $vendor->setPhoto($filename);

        $this->vendorManager->update($vendor);
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