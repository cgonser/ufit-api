<?php

declare(strict_types=1);

namespace App\Vendor\Service;

use App\Core\Service\ImageUploader;
use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\VendorInvalidPhotoException;
use GuzzleHttp\Client;
use Intervention\Image\ImageManagerStatic;
use League\Flysystem\FilesystemInterface;

class VendorPhotoManager extends ImageUploader
{
    public const MAX_WIDTH = 1024;
    public const MAX_HEIGHT = 1024;

    public function __construct(
        private VendorManager $vendorManager,
        private FilesystemInterface $vendorPhotoFileSystem,
    ) {
    }

    public function uploadPhoto(Vendor $vendor, string $photoContents, ?string $mimeType = null): void
    {
        try {
            $imageFile = tmpfile();
            $imagePath = stream_get_meta_data($imageFile)['uri'];
            fwrite($imageFile, $photoContents);

            $filesize = getimagesize($imagePath);
            $this->validateImageSize($filesize[0], $filesize[1]);
            $this->allocateMemory($filesize[0], $filesize[1]);

            $image = ImageManagerStatic::make($imageFile);

            if (false === $image) {
                throw new VendorInvalidPhotoException();
            }

            $image->fit(self::MAX_WIDTH, self::MAX_HEIGHT);
            $filename = $vendor->getId()
                ->toString().'.png';

            $this->vendorPhotoFileSystem->put($filename, $image->encode('png'), [
                'ACL' => 'public-read',
            ]);

            $vendor->setPhoto($filename);

            $this->vendorManager->update($vendor);
        } finally {
            $this->resetMemoryAllocation();
        }
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
        } catch (\Exception $exception) {
            echo $exception;

            return false;
        }
    }
}
