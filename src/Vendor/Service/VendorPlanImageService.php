<?php

namespace App\Vendor\Service;

use App\Vendor\Entity\VendorPlan;
use App\Vendor\Exception\VendorPlanInvalidImageException;
use App\Vendor\Repository\VendorPlanRepository;
use League\Flysystem\FilesystemInterface;

class VendorPlanImageService
{
    const FILE_PATH = 'plans/';

    private VendorPlanRepository $vendorPlanRepository;

    private FilesystemInterface $filesystem;

    public function __construct(
        VendorPlanRepository $vendorPlanRepository,
        FilesystemInterface $vendorPhotoFileSystem
    ) {
        $this->vendorPlanRepository = $vendorPlanRepository;
        $this->filesystem = $vendorPhotoFileSystem;
    }

    public function uploadImage(VendorPlan $vendorPlan, string $photoContents)
    {
        $photoContents = $this->decodePhotoContents($photoContents);

        $imageInfo = getimagesizefromstring($photoContents);

        if (false === $imageInfo) {
            throw new VendorPlanInvalidImageException();
        }

        $extension = explode('/', $imageInfo['mime'])[1];

        $filename = self::FILE_PATH.$vendorPlan->getId()->toString().'.'.$extension;

        $this->filesystem->put($filename, $photoContents, ['ACL' => 'public-read']);

        $vendorPlan->setImage($filename);

        $this->vendorPlanRepository->save($vendorPlan);
    }

    public function decodePhotoContents(string $photoContents): ?string
    {
        return null !== $photoContents
            ? base64_decode($photoContents)
            : null;
    }
}
