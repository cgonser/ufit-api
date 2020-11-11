<?php

namespace App\Vendor\Provider;

use App\Vendor\Entity\Vendor;
use App\Vendor\Entity\VendorPlan;
use App\Vendor\Exception\VendorPlanNotFoundException;
use App\Vendor\Repository\VendorPlanRepository;
use Ramsey\Uuid\UuidInterface;

class VendorPlanProvider
{
    private VendorPlanRepository $vendorPlanRepository;

    public function __construct(VendorPlanRepository $vendorPlanRepository)
    {
        $this->vendorPlanRepository = $vendorPlanRepository;
    }

    public function get(UuidInterface $vendorPlanId): VendorPlan
    {
        /** @var VendorPlan|null $vendorPlan */
        $vendorPlan = $this->vendorPlanRepository->find($vendorPlanId);

        if (!$vendorPlan) {
            throw new VendorPlanNotFoundException();
        }

        return $vendorPlan;
    }

    public function getByVendorAndId(Vendor $vendor, UuidInterface $vendorPlanId): VendorPlan
    {
        /** @var VendorPlan|null $vendorPlan */
        $vendorPlan = $this->vendorPlanRepository->findOneBy([
            'id' => $vendorPlanId,
            'vendor' => $vendor,
        ]);

        if (!$vendorPlan) {
            throw new VendorPlanNotFoundException();
        }

        return $vendorPlan;
    }

    public function findVendorPlans(Vendor $vendor): array
    {
        return $this->vendorPlanRepository->findBy(['vendor' => $vendor]);
    }
}