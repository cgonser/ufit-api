<?php

declare(strict_types=1);

namespace App\Program\Provider;

use App\Program\Entity\Program;
use App\Vendor\Entity\Vendor;
use Ramsey\Uuid\UuidInterface;

class VendorProgramProvider extends ProgramProvider
{
    public function getByVendorAndId(Vendor $vendor, UuidInterface $programId): ?Program
    {
        /** @var Program|null $program */
        $program = $this->repository->findOneBy([
            'id' => $programId,
            'vendor' => $vendor,
        ]);

        if ($program === null) {
            $this->throwNotFoundException();
        }

        return $program;
    }

    public function findByVendor(Vendor $vendor): array
    {
        return $this->repository->findBy([
            'vendor' => $vendor,
        ]);
    }

    /**
     * @return string[]
     */
    protected function getSearchableFields(): array
    {
        return ['name', 'goals'];
    }

    /**
     * @return string[]
     */
    protected function getFilterableFields(): array
    {
        return ['vendorId', 'isTemplate', 'isActive'];
    }
}
