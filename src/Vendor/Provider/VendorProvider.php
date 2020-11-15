<?php

namespace App\Vendor\Provider;

use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\VendorNotFoundException;
use App\Vendor\Repository\VendorRepository;
use Ramsey\Uuid\UuidInterface;

class VendorProvider
{
    private VendorRepository $vendorRepository;

    public function __construct(VendorRepository $vendorRepository)
    {
        $this->vendorRepository = $vendorRepository;
    }

    public function get(UuidInterface $vendorId): Vendor
    {
        /** @var Vendor|null $vendor */
        $vendor = $this->vendorRepository->find($vendorId);

        if (!$vendor) {
            throw new VendorNotFoundException();
        }

        return $vendor;
    }

    public function findOneByEmail(string $emailAddress): ?Vendor
    {
        return $this->vendorRepository->findOneBy(['email' => $emailAddress]);
    }

    public function findOneBySlug(string $slug): ?Vendor
    {
        return $this->vendorRepository->findOneBy(['slug' => $slug]);
    }

    public function findAll(): array
    {
        return $this->vendorRepository->findAll();
    }
}
