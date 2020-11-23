<?php

namespace App\Vendor\ResponseMapper;

use App\Vendor\Dto\VendorPlanDto;
use App\Vendor\Entity\VendorPlan;

class VendorPlanResponseMapper
{
    private VendorResponseMapper $vendorResponseMapper;

    private QuestionnaireResponseMapper $questionnaireResponseMapper;

    public function __construct(
        VendorResponseMapper $vendorResponseMapper,
        QuestionnaireResponseMapper $questionnaireResponseMapper
    ) {
        $this->vendorResponseMapper = $vendorResponseMapper;
        $this->questionnaireResponseMapper = $questionnaireResponseMapper;
    }

    public function map(VendorPlan $vendorPlan, bool $mapVendor = false): VendorPlanDto
    {
        $vendorPlanDto = new VendorPlanDto();
        $vendorPlanDto->id = $vendorPlan->getId()->toString();
        $vendorPlanDto->name = $vendorPlan->getName() ?? '';
        $vendorPlanDto->currency = $vendorPlan->getCurrency()->getCode();
        $vendorPlanDto->durationDays = $vendorPlan->getDuration()->d;
        $vendorPlanDto->durationMonths = $vendorPlan->getDuration()->m;
        $vendorPlanDto->price = $vendorPlan->getPrice();

        if ($mapVendor) {
            $vendorPlanDto->vendor = $this->vendorResponseMapper->map($vendorPlan->getVendor());
        } else {
            $vendorPlanDto->vendorId = $vendorPlan->getVendor()->getId()->toString();
        }

        $vendorPlanDto->questionnaireId = null !== $vendorPlan->getQuestionnaire()
            ? $vendorPlan->getQuestionnaire()->getId()->toString()
            : null;

        return $vendorPlanDto;
    }

    public function mapMultiple(array $vendorPlans, bool $mapVendor = false): array
    {
        $vendorPlanDtos = [];

        foreach ($vendorPlans as $vendorPlan) {
            $vendorPlanDtos[] = $this->map($vendorPlan, $mapVendor);
        }

        return $vendorPlanDtos;
    }
}
