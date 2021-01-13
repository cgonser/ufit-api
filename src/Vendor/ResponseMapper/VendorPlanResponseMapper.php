<?php

namespace App\Vendor\ResponseMapper;

use App\Vendor\Dto\VendorPlanDto;
use App\Vendor\Entity\VendorPlan;

class VendorPlanResponseMapper
{
    private QuestionnaireResponseMapper $questionnaireResponseMapper;

    public function __construct(QuestionnaireResponseMapper $questionnaireResponseMapper)
    {
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
