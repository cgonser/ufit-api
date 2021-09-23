<?php

declare(strict_types=1);

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
    public function __construct(
        private VendorPlanRepository $vendorPlanRepository,
        private VendorPlanProvider $vendorPlanProvider,
        private EntityValidator $entityValidator,
        private SluggerInterface $slugger
    ) {
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
        $slug = strtolower($this->slugger->slug($vendorPlan->getName())->toString());

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

        if (! $existingVendorPlan instanceof VendorPlan) {
            return true;
        }

        return ! $vendorPlan->isNew() && $existingVendorPlan->getId()
            ->equals($vendorPlan->getId());
    }

    private function prepareVendorPlan(VendorPlan $vendorPlan): void
    {
        if (null === $vendorPlan->getSlug() && null !== $vendorPlan->getName()) {
            $vendorPlan->setSlug($this->generateSlug($vendorPlan));
        }
    }

    private function validateVendorPlan(VendorPlan $vendorPlan): void
    {
        $this->entityValidator->validate($vendorPlan);

        if ($vendorPlan->isRecurring() && ! $vendorPlan->getDuration()) {
            throw new VendorPlanInvalidDurationException();
        }

        if (null !== $vendorPlan->getSlug() && ! $this->isSlugUnique($vendorPlan, $vendorPlan->getSlug())) {
            throw new VendorPlanSlugInUseException();
        }
    }
}
