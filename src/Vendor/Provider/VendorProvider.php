<?php

namespace App\Vendor\Provider;

use App\Vendor\Repository\VendorRepository;

class VendorProvider
{
    private VendorRepository $vendorRepository;

    public function __construct(VendorRepository $vendorRepository)
    {
        $this->vendorRepository = $vendorRepository;
    }
}