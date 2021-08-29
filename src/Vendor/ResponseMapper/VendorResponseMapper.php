<?php

declare(strict_types=1);

namespace App\Vendor\ResponseMapper;

use App\Vendor\Dto\VendorDto;
use App\Vendor\Entity\Vendor;
use App\Vendor\Entity\VendorPlan;
use Aws\S3\S3Client;
use League\Flysystem\FilesystemInterface;

class VendorResponseMapper
{
    public function __construct(
        private VendorPlanResponseMapper $vendorPlanResponseMapper,
        private S3Client $s3Client,
        private string $vendorPhotoS3Bucket,
        private FilesystemInterface $vendorPhotoFileSystem,
    ) {
    }

    public function mapBaseData(Vendor $vendor): VendorDto
    {
        $vendorDto = new VendorDto();
        $vendorDto->id = $vendor->getId()
            ->toString();
        $vendorDto->name = $vendor->getName();
        $vendorDto->displayName = $vendor->getDisplayName();
        $vendorDto->slug = $vendor->getSlug();
        $vendorDto->biography = $vendor->getBiography();
        $vendorDto->socialLinks = $vendor->getSocialLinks();
        $vendorDto->country = $vendor->getCountry();

        if (null !== $vendor->getPhoto()) {
            $vendorDto->photo = $this->s3Client->getObjectUrl($this->vendorPhotoS3Bucket, $vendor->getPhoto());
        }

        return $vendorDto;
    }

    public function mapPublic(Vendor $vendor): VendorDto
    {
        $vendorDto = $this->mapBaseData($vendor);
        $vendorDto->plans = [];

        /** @var VendorPlan $collection */
        foreach ($vendor->getPlans() as $collection) {
            if ($collection->isActive() && $collection->isVisible()) {
                $vendorDto->plans[] = $this->vendorPlanResponseMapper->map($collection);
            }
        }

        return $vendorDto;
    }

    public function map(Vendor $vendor, bool $mapPlans = true): VendorDto
    {
        $vendorDto = $this->mapBaseData($vendor);
        $vendorDto->email = $vendor->getEmail();
        $vendorDto->allowEmailMarketing = $vendor->allowEmailMarketing();
        $vendorDto->timezone = $vendor->getTimezone();
        $vendorDto->locale = $vendor->getLocale();

        if ($mapPlans) {
            $vendorDto->plans = $this->vendorPlanResponseMapper->mapMultiple($vendor->getPlans()->toArray());
        }

        return $vendorDto;
    }

    /**
     * @return VendorDto[]
     */
    public function mapMultiple(array $vendors): array
    {
        $vendorDtos = [];

        foreach ($vendors as $vendor) {
            $vendorDtos[] = $this->map($vendor);
        }

        return $vendorDtos;
    }
}
