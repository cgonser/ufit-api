<?php

namespace App\Vendor\Service;

use App\Core\Provider\CurrencyProvider;
use App\Vendor\Entity\Vendor;
use App\Vendor\Entity\VendorPlan;
use App\Vendor\Exception\VendorPlanInvalidDurationException;
use App\Vendor\Provider\VendorPlanProvider;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Repository\VendorPlanRepository;
use App\Vendor\Request\VendorPlanCreateRequest;
use App\Vendor\Request\VendorPlanUpdateRequest;

class VendorPlanService
{
    private VendorPlanRepository $vendorPlanRepository;

    private VendorPlanProvider $vendorPlanProvider;

    private VendorProvider $vendorProvider;

    private CurrencyProvider $currencyProvider;

    public function __construct(
        VendorPlanRepository $vendorPlanRepository,
        VendorPlanProvider $vendorPlanProvider,
        VendorProvider $vendorProvider,
        CurrencyProvider $currencyProvider
    ) {
        $this->vendorPlanRepository = $vendorPlanRepository;
        $this->vendorPlanProvider = $vendorPlanProvider;
        $this->vendorProvider = $vendorProvider;
        $this->currencyProvider = $currencyProvider;
    }

    public function create(Vendor $vendor, VendorPlanCreateRequest $vendorPlanCreateRequest): VendorPlan
    {
        $vendorPlan = new VendorPlan();
        $vendorPlan->setVendor($vendor);
        $vendorPlan->setName($vendorPlanCreateRequest->name);
        $vendorPlan->setPrice($vendorPlanCreateRequest->price);
        $vendorPlan->setCurrency($this->currencyProvider->getByCode($vendorPlanCreateRequest->currency));
        $vendorPlan->setDuration(
            $this->prepareDuration($vendorPlanCreateRequest->durationMonths, $vendorPlanCreateRequest->durationDays)
        );

        $this->vendorPlanRepository->save($vendorPlan);

        return $vendorPlan;
    }

    public function update(VendorPlan $vendorPlan, VendorPlanUpdateRequest $vendorPlanUpdateRequest)
    {
        $vendorPlan->setName($vendorPlanUpdateRequest->name);
        $vendorPlan->setPrice($vendorPlanUpdateRequest->price);
        $vendorPlan->setCurrency($this->currencyProvider->getByCode($vendorPlanUpdateRequest->currency));
        $vendorPlan->setDuration(
            $this->prepareDuration($vendorPlanUpdateRequest->durationMonths, $vendorPlanUpdateRequest->durationDays)
        );

        $this->vendorPlanRepository->save($vendorPlan);
    }

    private function prepareDuration(string $durationMonths, string $durationDays): \DateInterval
    {
        $durationString = 'P';

        if (null !== $durationMonths && $durationMonths > 0) {
            $durationString .= sprintf('%sM', $durationMonths);
        }
        if (null !== $durationDays && $durationDays > 0) {
            $durationString .= sprintf('%sD', $durationDays);
        }

        try {
            return new \DateInterval($durationString);
        } catch (\Exception $e) {
            throw new VendorPlanInvalidDurationException();
        }
    }
}
