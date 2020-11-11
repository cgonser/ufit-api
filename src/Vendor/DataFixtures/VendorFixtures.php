<?php

namespace App\Vendor\DataFixtures;

use App\Vendor\Request\VendorCreateRequest;
use App\Vendor\Service\VendorService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VendorFixtures extends Fixture
{
    private VendorService $vendorService;

    public function __construct(VendorService $vendorService)
    {
        $this->vendorService = $vendorService;
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadVendors($manager);
    }

    private function loadVendors(ObjectManager $manager): void
    {
        foreach ($this->getVendorData() as [$name, $password, $email]) {
            $vendorCreateRequest = new VendorCreateRequest();
            $vendorCreateRequest->name = $name;
            $vendorCreateRequest->password = $password;
            $vendorCreateRequest->email = $email;

            $vendor = $this->vendorService->create($vendorCreateRequest);

            $this->addReference($email, $vendor);
        }

        $manager->flush();
    }

    private function getVendorData(): array
    {
        return [
            ['Vendor 1', '123', 'vendor1@vendor.com'],
            ['Vendor 2', '123', 'vendor2@vendor.com'],
            ['Vendor 3', '123', 'vendor3@vendor.com'],
        ];
    }
}
