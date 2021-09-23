<?php

declare(strict_types=1);

namespace App\Program\ResponseMapper;

use App\Program\Dto\ProgramAssetDto;
use App\Program\Entity\ProgramAsset;
use Aws\S3\S3Client;

class ProgramAssetResponseMapper
{
    public function __construct(private S3Client $s3Client, private string $programAssetS3Bucket)
    {
    }

    public function map(ProgramAsset $programAsset): ProgramAssetDto
    {
        $programAssetDto = new ProgramAssetDto();
        $programAssetDto->id = $programAsset->getId()->toString();
        $programAssetDto->programId = $programAsset->getProgram()->getId()->toString();
        $programAssetDto->title = $programAsset->getTitle();
        $programAssetDto->type = $programAsset->getType();
        if (null !== $programAsset->getFilename()) {
            $programAssetDto->url = $this->prepareAssetUrl($programAsset->getFilename());
        }
        $programAssetDto->createdAt = $programAsset->getCreatedAt()?->format(\DateTimeInterface::ATOM);

        return $programAssetDto;
    }

    /**
     * @return ProgramAssetDto[]
     */
    public function mapMultiple(array $programAssets): array
    {
        $dtos = [];

        foreach ($programAssets as $programAsset) {
            $dtos[] = $this->map($programAsset);
        }

        return $dtos;
    }

    private function prepareAssetUrl(string $filename): string
    {
        $command = $this->s3Client->getCommand('GetObject', [
            'Bucket' => $this->programAssetS3Bucket,
            'Key' => $filename,
        ]);

        $request = $this->s3Client->createPresignedRequest($command, '+15 minutes');

        return (string) $request->getUri();
    }
}
