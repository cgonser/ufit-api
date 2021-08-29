<?php

declare(strict_types=1);

namespace App\Vendor\Service;

use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\VendorInvalidPhotoException;
use Exception;
use GuzzleHttp\Client;
use Intervention\Image\ImageManagerStatic;
use League\Flysystem\FilesystemInterface;

class VendorPhotoManager
{
    public function __construct(
        private VendorManager $vendorManager,
        private FilesystemInterface $vendorPhotoFileSystem,
    ) {
    }

    public function uploadPhoto(Vendor $vendor, string $photoContents): void
    {
        $image = ImageManagerStatic::make($photoContents);
        echo "mime=".$image->mime().PHP_EOL;

        if (false === $image) {
            throw new VendorInvalidPhotoException();
        }

        $filename = $vendor->getId()
            ->toString().'.png';

        $this->vendorPhotoFileSystem->put($filename, $image->encode('png'), [
            'ACL' => 'public-read',
        ]);

        $vendor->setPhoto($filename);

        $this->vendorManager->update($vendor);
    }

    public function uploadFromUrl(Vendor $vendor, string $photoUrl): bool
    {
        try {
            $httpClient = new Client();
            $response = $httpClient->get($photoUrl);

            $photoContents = $response->getBody()
                ->getContents();

            $this->uploadPhoto($vendor, $photoContents);

            return true;
        } catch (Exception $exception) {
            echo $exception;

            return false;
        }
    }
}
