<?php

namespace App\Vendor\Service;

use App\Core\Validation\EntityValidator;
use App\Vendor\Entity\VendorPlan;
use App\Vendor\Exception\VendorPlanInvalidDurationException;
use App\Vendor\Exception\VendorPlanSlugInUseException;
use App\Vendor\Provider\VendorPlanProvider;
use App\Vendor\Repository\VendorPlanRepository;
use Symfony\Component\String\Slugger\SluggerInterface;

class VendorPlanManager
{
    private VendorPlanRepository $vendorPlanRepository;

    private VendorPlanProvider $vendorPlanProvider;

    private EntityValidator $validator;

    private SluggerInterface $slugger;

    public function __construct(
        VendorPlanRepository $vendorPlanRepository,
        VendorPlanProvider $vendorPlanProvider,
        EntityValidator $validator,
        SluggerInterface $slugger
    ) {
        $this->vendorPlanRepository = $vendorPlanRepository;
        $this->vendorPlanProvider = $vendorPlanProvider;
        $this->validator = $validator;
        $this->slugger = $slugger;
    }

    public function create(VendorPlan $vendorPlan): void
    {
        $this->prepareVendorPlan($vendorPlan);
        $this->validateVendorPlan($vendorPlan);

        $this->vendorPlanRepository->save($vendorPlan);
    }

    public function update(VendorPlan $vendorPlan): void
    {
        $this->prepareVendorPlan($vendorPlan);
        $this->validateVendorPlan($vendorPlan);

        $this->vendorPlanRepository->save($vendorPlan);
    }

    public function delete(VendorPlan $vendorPlan): void
    {
        $this->vendorPlanRepository->delete($vendorPlan);
    }

    public function generateSlug(VendorPlan $vendorPlan, ?int $suffix = null): string
    {
        $slug = strtolower($this->slugger->slug($vendorPlan->getName()));

        if (null !== $suffix) {
            $slug .= '-'.$suffix;
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

        if (!$vendorPlan->isNew() && $existingVendorPlan->getId()->equals($vendorPlan->getId())) {
            return true;
        }

        return false;
    }

    private function prepareVendorPlan(VendorPlan $vendorPlan)
    {
        if (null === $vendorPlan->getSlug() && null !== $vendorPlan->getName()) {
            $vendorPlan->setSlug($this->generateSlug($vendorPlan));
        }
    }

    private function validateVendorPlan(VendorPlan $vendorPlan)
    {
        $this->validator->validate($vendorPlan);

        if ($vendorPlan->isRecurring() && !$vendorPlan->getDuration()) {
            throw new VendorPlanInvalidDurationException();
        }

        if (null !== $vendorPlan->getSlug() && !$this->isSlugUnique($vendorPlan, $vendorPlan->getSlug())) {
            throw new VendorPlanSlugInUseException();
        }
    }
}
