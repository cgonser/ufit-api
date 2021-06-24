<?php

namespace App\Vendor\ResponseMapper;

use App\Vendor\Dto\VendorDto;
use App\Vendor\Entity\Vendor;
use App\Vendor\Entity\VendorPlan;
use Aws\S3\S3Client;

class VendorResponseMapper
{
    private VendorPlanResponseMapper $vendorPlanResponseMapper;

    private S3Client $s3Client;

    private string $vendorPhotoS3Bucket;

    public function __construct(
        VendorPlanResponseMapper $vendorPlanResponseMapper,
        S3Client $s3Client,
        string $vendorPhotoS3Bucket
    ) {
        $this->vendorPlanResponseMapper = $vendorPlanResponseMapper;
        $this->s3Client = $s3Client;
        $this->vendorPhotoS3Bucket = $vendorPhotoS3Bucket;
    }

    public function mapBaseData(Vendor $vendor): VendorDto
    {
        $vendorDto = new VendorDto();
        $vendorDto->id = $vendor->getId()->toString();
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

        /** @var VendorPlan $vendorPlan */
        foreach ($vendor->getPlans() as $vendorPlan) {
            if ($vendorPlan->isActive() && $vendorPlan->isVisible()) {
                $vendorDto->plans[] = $this->vendorPlanResponseMapper->map($vendorPlan);
            }
        }

        return $vendorDto;
    }

    public function map(Vendor $vendor, bool $mapPlans = true): VendorDto
    {
        $vendorDto = $this->mapBaseData($vendor);
        $vendorDto->email = $vendor->getEmail();
        $vendorDto->allowEmailMarketing = $vendor->allowEmailMarketing();
        $vendorDto->country = $vendor->getCountry();

        if ($mapPlans) {
            $vendorDto->plans = $this->vendorPlanResponseMapper->mapMultiple($vendor->getPlans()->toArray());
        }

        return $vendorDto;
    }

    public function mapMultiple(array $vendors): array
    {
        $vendorDtos = [];

        foreach ($vendors as $vendor) {
            $vendorDtos[] = $this->map($vendor);
        }

        return $vendorDtos;
    }
}