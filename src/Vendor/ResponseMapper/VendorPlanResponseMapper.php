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
        $vendorPlanDto->durationDays = $vendorPlan->getDuration()->d;
        $vendorPlanDto->durationMonths = $vendorPlan->getDuration()->m;
        $vendorPlanDto->price = $vendorPlan->getPrice();

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
