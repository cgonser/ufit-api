<?php

namespace App\Vendor\Service;

use App\Core\Validation\EntityValidator;
use App\Vendor\Entity\Vendor;
use App\Vendor\Repository\VendorRepository;

class VendorManager
{
    private VendorRepository $vendorRepository;

    private EntityValidator $validator;

    public function __construct(
        VendorRepository $vendorRepository,
        EntityValidator $validator
    ) {
        $this->vendorRepository = $vendorRepository;
        $this->validator = $validator;
    }

    public function create(Vendor $vendor): void
    {
        $this->validateVendor($vendor);

        $this->vendorRepository->save($vendor);
    }

    public function update(Vendor $vendor): void
    {
        $this->validateVendor($vendor);

        $this->vendorRepository->save($vendor);
    }

    public function delete(Vendor $vendor): void
    {
        $this->vendorRepository->delete($vendor);
    }

    private function validateVendor(Vendor $vendor)
    {
        $this->validator->validate($vendor);
    }
}
