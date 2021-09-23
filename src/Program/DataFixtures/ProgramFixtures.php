<?php

declare(strict_types=1);

namespace App\Program\DataFixtures;

use Iterator;
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
    public function __construct(private VendorProvider $vendorProvider, private ProgramManager $programManager)
    {
    }

    public function load(ObjectManager $objectManager): void
    {
        foreach ($this->vendorProvider->findAll() as $vendor) {
            $this->loadVendor($vendor);
        }

        $objectManager->flush();
    }

    /**
     * @return array<class-string<VendorFixtures>>
     */
    public function getDependencies(): array
    {
        return [VendorFixtures::class];
    }

    private function loadVendor(Vendor $vendor): void
    {
        foreach ($this->getData() as $programRequest) {
            $this->programManager->createFromRequest($vendor, $programRequest);
        }
    }

    /**
     * @return Iterator<ProgramRequest>
     */
    private function getData(): Iterator
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
