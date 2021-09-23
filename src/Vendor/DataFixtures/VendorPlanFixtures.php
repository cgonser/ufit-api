<?php

declare(strict_types=1);

namespace App\Vendor\DataFixtures;

use App\Localization\Provider\CurrencyProvider;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Request\VendorPlanRequest;
use App\Vendor\Service\VendorPlanRequestManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Iterator;

class VendorPlanFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private VendorProvider $vendorProvider,
        private CurrencyProvider $currencyProvider,
        private VendorPlanRequestManager $vendorPlanManager
    ) {
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
        foreach ($this->getData() as $vendorPlanRequest) {
            $this->vendorPlanManager->createFromRequest($vendor, $vendorPlanRequest);
        }
    }

    /**
     * @return Iterator<VendorPlanRequest>
     */
    private function getData(): Iterator
    {
        $currencies = $this->currencyProvider->findAll();
        $currency = $currencies[array_rand($currencies)];

        $price = 10 * random_int(3, 30);

        $vendorPlanRequest = new VendorPlanRequest();
        $vendorPlanRequest->name = 'Monthly';
        $vendorPlanRequest->isRecurring = true;
        $vendorPlanRequest->durationMonths = 1;
        $vendorPlanRequest->isVisible = true;
        $vendorPlanRequest->currency = $currency->getCode();
        $vendorPlanRequest->features = ['Photos and videos', 'Weekly updates', 'Detailed routine'];
        $vendorPlanRequest->price = (string) $price;

        yield $vendorPlanRequest;

        $vendorPlanRequest->name = 'Weekly';
        $vendorPlanRequest->durationDays = 7;
        $vendorPlanRequest->durationMonths = null;
        $vendorPlanRequest->price = (string) ($price / 4);

        yield $vendorPlanRequest;

        $vendorPlanRequest->name = 'Single';
        $vendorPlanRequest->isRecurring = false;
        $vendorPlanRequest->durationDays = null;
        $vendorPlanRequest->durationMonths = null;
        $vendorPlanRequest->price = (string) ($price * 0.75);

        yield $vendorPlanRequest;
    }
}
