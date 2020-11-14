<?php

namespace App\Vendor\Service;

use App\Core\Provider\CurrencyProvider;
use App\Vendor\Entity\Vendor;
use App\Vendor\Entity\VendorPlan;
use App\Vendor\Exception\VendorPlanInvalidDurationException;
use App\Vendor\Exception\VendorPlanNotFoundException;
use App\Vendor\Provider\VendorPlanProvider;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Repository\VendorPlanRepository;
use App\Vendor\Request\VendorPlanCreateRequest;
use App\Vendor\Request\VendorPlanUpdateRequest;
use Symfony\Component\String\Slugger\SluggerInterface;

class VendorPlanService
{
    private VendorPlanRepository $vendorPlanRepository;

    private VendorPlanProvider $vendorPlanProvider;

    private VendorProvider $vendorProvider;

    private CurrencyProvider $currencyProvider;

    private SluggerInterface $slugger;

    public function __construct(
        VendorPlanRepository $vendorPlanRepository,
        VendorPlanProvider $vendorPlanProvider,
        VendorProvider $vendorProvider,
        CurrencyProvider $currencyProvider,
        SluggerInterface $slugger
    ) {
        $this->vendorPlanRepository = $vendorPlanRepository;
        $this->vendorPlanProvider = $vendorPlanProvider;
        $this->vendorProvider = $vendorProvider;
        $this->currencyProvider = $currencyProvider;
        $this->slugger = $slugger;
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
        $vendorPlan->setSlug($this->generateSlug($vendorPlan));

        $this->vendorPlanRepository->save($vendorPlan);

        return $vendorPlan;
    }

    public function update(VendorPlan $vendorPlan, VendorPlanUpdateRequest $vendorPlanUpdateRequest)
    {
        if (!$this->isSlugUnique($vendorPlan, $vendorPlanUpdateRequest->slug)) {
            throw new VendorPlanNotFoundException();
        }

        $vendorPlan->setName($vendorPlanUpdateRequest->name);
        $vendorPlan->setPrice($vendorPlanUpdateRequest->price);
        $vendorPlan->setCurrency($this->currencyProvider->getByCode($vendorPlanUpdateRequest->currency));
        $vendorPlan->setDuration(
            $this->prepareDuration($vendorPlanUpdateRequest->durationMonths, $vendorPlanUpdateRequest->durationDays)
        );
        $vendorPlan->setSlug($vendorPlanUpdateRequest->slug);

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

    public function delete(VendorPlan $vendorPlan)
    {
        $this->vendorPlanRepository->delete($vendorPlan);
    }

    public function generateSlug(VendorPlan $vendorPlan, ?int $suffix = null): string
    {
        $slug = strtolower($this->slugger->slug($vendorPlan->getName()));

        if (null !== $suffix) {
            $slug .= '-'.(string) $suffix;
        }

        if ($this->isSlugUnique($vendorPlan, $slug)) {
            return $slug;
        }

        $suffix = null !== $suffix ? $suffix + 1 : 1;

        return $this->generateSlug($vendorPlan, $suffix);
    }

    private function isSlugUnique(VendorPlan $vendorPlan, string $slug): bool
    {
        $existingVendorPlan = $this->vendorPlanProvider->findOneByVendorAndSlug($vendorPlan->getVendor(), $slug);

        if (!$existingVendorPlan) {
            return true;
        }

        if (!$vendorPlan->isNew() && $existingVendorPlan->getId()->toString() == $vendorPlan->getId()->toString()) {
            return true;
        }

        return false;
    }
}
