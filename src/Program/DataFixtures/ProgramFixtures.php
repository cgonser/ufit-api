<?php

declare(strict_types=1);

namespace App\Program\DataFixtures;

use App\Program\Request\ProgramRequest;
use App\Program\Service\ProgramManager;
use App\Vendor\DataFixtures\VendorFixtures;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorProvider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    private VendorProvider $vendorProvider;

    private ProgramManager $programManager;

    public function __construct(VendorProvider $vendorProvider, ProgramManager $programManager)
    {
        $this->vendorProvider = $vendorProvider;
        $this->programManager = $programManager;
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->vendorProvider->findAll() as $vendor) {
            $this->loadVendor($vendor);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [VendorFixtures::class];
    }

    private function loadVendor(Vendor $vendor)
    {
        foreach ($this->getData() as $programRequest) {
            $this->programManager->createFromRequest($vendor, $programRequest);
        }
    }

    private function getData(): \Iterator
    {
        $programRequest = new ProgramRequest();
        $programRequest->isTemplate = true;
        $programRequest->goals = ['fat loss'];
        $programRequest->level = 'Beginner';
        $programRequest->name = 'Quick fat loss';
        $programRequest->description = 'Just eat less';

        yield $programRequest;

        $programRequest = new ProgramRequest();
        $programRequest->isTemplate = true;
        $programRequest->goals = ['strength', 'conditioning'];
        $programRequest->level = 'Advanced';
        $programRequest->name = 'Superman';
        $programRequest->description = 'Go to Krypton';

        yield $programRequest;
    }
}
