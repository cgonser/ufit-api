<?php

namespace App\Vendor\Service;

use App\Vendor\Entity\VendorPlan;
use App\Vendor\Exception\VendorPlanInvalidImageException;
use Intervention\Image\ImageManagerStatic as Image;
use League\Flysystem\FilesystemInterface;

class VendorPlanImageManager
{
    public const FILE_PATH = 'plans/';

    private VendorPlanManager $vendorPlanManager;
    private FilesystemInterface $filesystem;

    public function __construct(
        VendorPlanManager $vendorPlanManager,
        FilesystemInterface $vendorPhotoFileSystem
    ) {
        $this->vendorPlanManager = $vendorPlanManager;
        $this->filesystem = $vendorPhotoFileSystem;
    }

    public function uploadPhoto(VendorPlan $vendorPlan, string $photoContents): void
    {
        $image = Image::make($photoContents);

        if (false === $image) {
            throw new VendorPlanInvalidImageException();
        }

        $filename = self::FILE_PATH.$vendorPlan->getId()->toString().'.png';

        $this->filesystem->put(
            $filename,
            $image->encode('png'),
            ['ACL' => 'public-read']
        );

        $vendorPlan->setImage($filename);

        $this->vendorPlanManager->update($vendorPlan);
    }
}
