<?php

namespace App\Vendor\DataFixtures;

use App\Localization\Provider\CurrencyProvider;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Request\VendorPlanRequest;
use App\Vendor\Service\VendorPlanManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class VendorPlanFixtures extends Fixture implements DependentFixtureInterface
{
    private VendorProvider $vendorProvider;

    private VendorPlanManager $vendorPlanService;

    private CurrencyProvider $currencyProvider;

    public function __construct(
        VendorProvider $vendorProvider,
        CurrencyProvider $currencyProvider,
        VendorPlanManager $vendorPlanService
    ) {
        $this->vendorProvider = $vendorProvider;
        $this->vendorPlanService = $vendorPlanService;
        $this->currencyProvider = $currencyProvider;
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->vendorProvider->findAll() as $vendor) {
            $this->loadVendor($vendor);
        }

        $manager->flush();
    }

    private function loadVendor(Vendor $vendor)
    {
        foreach ($this->getData() as $vendorPlanRequest) {
            $this->vendorPlanService->create($vendor, $vendorPlanRequest);
        }
    }

    private function getData(): \Iterator
    {
        $currencies = $this->currencyProvider->findAll();
        $currency = $currencies[array_rand($currencies)];

        $vendorPlanRequest = new VendorPlanRequest();
        $vendorPlanRequest->name = 'Monthly';
        $vendorPlanRequest->isRecurring = true;
        $vendorPlanRequest->durationMonths = 1;
        $vendorPlanRequest->isVisible = true;
        $vendorPlanRequest->currency = $currency->getCode();
        $vendorPlanRequest->features = [
            'Photos and videos',
            'Weekly updates',
            'Detailed routine',
        ];
        $vendorPlanRequest->price = 10 * rand(3, 30);

        yield $vendorPlanRequest;

        $vendorPlanRequest->name = 'Weekly';
        $vendorPlanRequest->durationDays = 7;
        $vendorPlanRequest->durationMonths = null;
        $vendorPlanRequest->price = $vendorPlanRequest->price / 4;

        yield $vendorPlanRequest;

        $vendorPlanRequest->name = 'Single';
        $vendorPlanRequest->isRecurring = false;
        $vendorPlanRequest->durationDays = null;
        $vendorPlanRequest->durationMonths = null;
        $vendorPlanRequest->price = $vendorPlanRequest->price * 0.75;

        yield $vendorPlanRequest;
    }

    public function getDependencies()
    {
        return [
            VendorFixtures::class,
        ];
    }
}
