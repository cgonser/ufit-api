<?php

namespace App\Vendor\DataFixtures;

use App\Vendor\Entity\Vendor;
use App\Vendor\Repository\VendorRepository;
use App\Vendor\Service\VendorManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class VendorFixtures extends Fixture
{
    private VendorManager $vendorManager;

    public function __construct(VendorManager $vendorManager)
    {
        $this->vendorManager = $vendorManager;
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadVendors($manager);
    }

    private function loadVendors(ObjectManager $manager): void
    {
        foreach ($this->getVendorData() as [$name, $password, $email]) {
            $vendor = new Vendor();
            $vendor->setName($name);
            $vendor->setPassword($password);
            $vendor->setEmail($email);

            $this->vendorManager->createVendor($vendor);

            $this->addReference($email, $vendor);
        }

        $manager->flush();
    }

    private function getVendorData(): array
    {
        return [
            ['Vendor', '123', 'vendor@gonser.eu'],
        ];
    }
}
