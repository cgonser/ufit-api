<?php

namespace App\Vendor\ResponseMapper;

use App\Vendor\Dto\VendorPlanDto;
use App\Vendor\Entity\VendorPlan;
use Aws\S3\S3Client;

class VendorPlanResponseMapper
{
    private QuestionnaireResponseMapper $questionnaireResponseMapper;

    private S3Client $s3Client;

    private string $vendorPhotoS3Bucket;

    public function __construct(
        QuestionnaireResponseMapper $questionnaireResponseMapper,
        S3Client $s3Client,
        string $vendorPhotoS3Bucket
    ) {
        $this->s3Client = $s3Client;
        $this->vendorPhotoS3Bucket = $vendorPhotoS3Bucket;
        $this->questionnaireResponseMapper = $questionnaireResponseMapper;
    }

    public function map(VendorPlan $vendorPlan, bool $mapQuestionnaire = true): VendorPlanDto
    {
        $vendorPlanDto = new VendorPlanDto();
        $vendorPlanDto->id = $vendorPlan->getId()->toString();
        $vendorPlanDto->vendorId = $vendorPlan->getVendor()->getId()->toString();
        $vendorPlanDto->name = $vendorPlan->getName() ?? '';
        $vendorPlanDto->currency = $vendorPlan->getCurrency()->getCode();
        $vendorPlanDto->durationDays = $vendorPlan->getDuration() ? $vendorPlan->getDuration()->d : null;
        $vendorPlanDto->durationMonths = $vendorPlan->getDuration() ? $vendorPlan->getDuration()->m : null;
        $vendorPlanDto->price = $vendorPlan->getPrice();
        $vendorPlanDto->isVisible = $vendorPlan->isVisible();
        $vendorPlanDto->isRecurring = $vendorPlan->isRecurring();
        $vendorPlanDto->description = $vendorPlan->getDescription();
        $vendorPlanDto->features = $vendorPlan->getFeatures();

        if ($mapQuestionnaire) {
            $vendorPlanDto->questionnaire = null !== $vendorPlan->getQuestionnaire()
                ? $this->questionnaireResponseMapper->map($vendorPlan->getQuestionnaire())
                : null;
        } else {
            $vendorPlanDto->questionnaireId = null !== $vendorPlan->getQuestionnaire()
                ? $vendorPlan->getQuestionnaire()->getId()->toString()
                : null;
        }

        if (null != $vendorPlan->getImage()) {
            $vendorPlanDto->image = $this->s3Client->getObjectUrl(
                $this->vendorPhotoS3Bucket,
                $vendorPlan->getImage()
            );
        }

        return $vendorPlanDto;
    }

    public function mapMultiple(array $vendorPlans, bool $mapQuestionnaire = false): array
    {
        $vendorPlanDtos = [];

        foreach ($vendorPlans as $vendorPlan) {
            $vendorPlanDtos[] = $this->map($vendorPlan, $mapQuestionnaire);
        }

        return $vendorPlanDtos;
    }
}
