<?php

namespace App\Vendor\ResponseMapper;

use App\Vendor\Dto\VendorDto;
use App\Vendor\Entity\Vendor;
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

    public function map(Vendor $vendor): VendorDto
    {
        $vendorDto = new VendorDto();
        $vendorDto->id = $vendor->getId()->toString();
        $vendorDto->name = $vendor->getName();
        $vendorDto->slug = $vendor->getSlug();
        $vendorDto->biography = $vendor->getBiography();
        // $vendorDto->email = $vendor->getEmail() ?? '';

        if (null != $vendor->getPhoto()) {
            $vendorDto->photo = $this->s3Client->getObjectUrl($this->vendorPhotoS3Bucket, $vendor->getPhoto());
        }

        $vendorDto->plans = $this->vendorPlanResponseMapper->mapMultiple($vendor->getPlans()->toArray());

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