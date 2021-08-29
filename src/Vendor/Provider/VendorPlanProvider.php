<?php

declare(strict_types=1);

namespace App\Vendor\Provider;

use App\Vendor\Entity\Vendor;
use App\Vendor\Entity\VendorPlan;
use App\Vendor\Exception\VendorPlanNotFoundException;
use App\Vendor\Repository\VendorPlanRepository;
use Ramsey\Uuid\UuidInterface;

class VendorPlanProvider
{
    public function __construct(
        private VendorPlanRepository $vendorPlanRepository
    ) {
    }

    public function get(UuidInterface $vendorPlanId): VendorPlan
    {
        /** @var VendorPlan|null $vendorPlan */
        $vendorPlan = $this->vendorPlanRepository->find($vendorPlanId);

        if (null === $vendorPlan) {
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

        if (null === $vendorPlan) {
            throw new VendorPlanNotFoundException();
        }

        return $vendorPlan;
    }

    /**
     * @return VendorPlan[]
     */
    public function findVendorPlans(Vendor $vendor): array
    {
        return $this->vendorPlanRepository->findBy([
            'vendor' => $vendor,
        ]);
    }

    public function findOneByVendorAndSlug(Vendor $vendor, string $slug): ?VendorPlan
    {
        return $this->vendorPlanRepository->findOneBy([
            'vendor' => $vendor,
            'slug' => $slug,
        ]);
    }
}
