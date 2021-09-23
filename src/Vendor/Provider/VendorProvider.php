<?php

declare(strict_types=1);

namespace App\Vendor\Provider;

use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\VendorNotFoundException;
use App\Vendor\Repository\VendorRepository;
use Ramsey\Uuid\UuidInterface;

class VendorProvider
{
    public function __construct(
        private VendorRepository $vendorRepository
    ) {
    }

    public function get(UuidInterface $vendorId): Vendor
    {
        /** @var Vendor|null $vendor */
        $vendor = $this->vendorRepository->find($vendorId);

        if (null === $vendor) {
            throw new VendorNotFoundException();
        }

        return $vendor;
    }

    public function findOneByEmail(string $emailAddress): ?object
    {
        return $this->vendorRepository->findOneBy([
            'email' => $emailAddress,
        ]);
    }

    public function findOneBySlug(string $slug): ?object
    {
        return $this->vendorRepository->findOneBy([
            'slug' => $slug,
        ]);
    }

    /**
     * @return mixed[]
     */
    public function findAll(?array $orderBy = []): array
    {
        return $this->vendorRepository->findBy([], $orderBy);
    }
}
