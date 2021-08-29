<?php

declare(strict_types=1);

namespace App\Vendor\Service;

use App\Vendor\Entity\VendorPlan;
use App\Vendor\Exception\VendorPlanInvalidImageException;
use Intervention\Image\ImageManagerStatic;
use League\Flysystem\FilesystemInterface;

class VendorPlanImageManager
{
    /**
     * @var string
     */
    public const FILE_PATH = 'plans/';

    public function __construct(
        private VendorPlanManager $vendorPlanManager,
        private FilesystemInterface $vendorPhotoFileSystem,
    ) {
    }

    public function uploadPhoto(VendorPlan $vendorPlan, string $photoContents): void
    {
        $image = ImageManagerStatic::make($photoContents);

        if (false === $image) {
            throw new VendorPlanInvalidImageException();
        }

        $filename = self::FILE_PATH.$vendorPlan->getId()->toString().'.png';

        $this->vendorPhotoFileSystem->put($filename, $image->encode('png'), [
            'ACL' => 'public-read',
        ]);

        $vendorPlan->setImage($filename);

        $this->vendorPlanManager->update($vendorPlan);
    }
}
