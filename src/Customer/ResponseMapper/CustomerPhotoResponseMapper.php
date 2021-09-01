<?php

declare(strict_types=1);

namespace App\Customer\ResponseMapper;

use App\Customer\Dto\CustomerPhotoDto;
use App\Customer\Entity\CustomerPhoto;
use Aws\S3\S3Client;

class CustomerPhotoResponseMapper
{
    private S3Client $s3Client;

    private string $customerPhotoS3Bucket;

    public function __construct(S3Client $s3Client, string $customerPhotoS3Bucket)
    {
        $this->s3Client = $s3Client;
        $this->customerPhotoS3Bucket = $customerPhotoS3Bucket;
    }

    public function map(CustomerPhoto $customerPhoto): CustomerPhotoDto
    {
        $customerPhotoDto = new CustomerPhotoDto();
        $customerPhotoDto->id = $customerPhoto->getId()->toString();
        $customerPhotoDto->customerId = $customerPhoto->getCustomer()->getId()->toString();
        $customerPhotoDto->title = $customerPhoto->getTitle() ?? '';
        $customerPhotoDto->description = $customerPhoto->getDescription() ?? '';
        $customerPhotoDto->takenAt = $customerPhoto->getTakenAt()?->format(\DateTimeInterface::ATOM);

        if (null !== $customerPhoto->getFilename()) {
            $cmd = $this->s3Client->getCommand('GetObject', [
                'Bucket' => $this->customerPhotoS3Bucket,
                'Key' => $customerPhoto->getFilename(),
            ]);

            $request = $this->s3Client->createPresignedRequest($cmd, '+15 minutes');
            $customerPhotoDto->url = (string) $request->getUri();
        }

        return $customerPhotoDto;
    }

    public function mapMultiple(array $customerPhotos): array
    {
        $customerPhotoDTOs = [];

        foreach ($customerPhotos as $customerPhoto) {
            $customerPhotoDTOs[] = $this->map($customerPhoto);
        }

        return $customerPhotoDTOs;
    }
}
