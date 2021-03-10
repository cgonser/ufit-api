<?php

namespace App\Subscription\DataFixtures;

use App\Customer\DataFixtures\CustomerFixtures;
use App\Customer\Entity\Customer;
use App\Customer\Provider\CustomerProvider;
use App\Subscription\Request\SubscriptionRequest;
use App\Subscription\Service\SubscriptionManager;
use App\Vendor\DataFixtures\VendorFixtures;
use App\Vendor\DataFixtures\VendorPlanFixtures;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorProvider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SubscriptionFixtures extends Fixture implements DependentFixtureInterface
{
    private SubscriptionManager $service;

    private CustomerProvider $customerProvider;

    private VendorProvider $vendorProvider;

    private array $vendors = [];

    public function __construct(
        SubscriptionManager $service,
        CustomerProvider $customerProvider,
        VendorProvider $vendorProvider
    ) {
        $this->service = $service;
        $this->customerProvider = $customerProvider;
        $this->vendorProvider = $vendorProvider;
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->customerProvider->findAll() as $customer) {
            $this->loadCustomer($customer);
        }

        $manager->flush();
    }

    private function loadCustomer(Customer $customer)
    {
        $vendors = $this->getVendors();
        /** @var Vendor $vendor */
        $vendor = $vendors[array_rand($vendors)];

        $vendorPlans = $vendor->getPlans();
        $vendorPlan = $vendorPlans->get(rand(0, $vendorPlans->count() - 1));

        $subscriptionRequest = new SubscriptionRequest();
        $subscriptionRequest->vendorPlanId = $vendorPlan->getId()->toString();

        $this->service->createFromCustomerRequest($customer, $subscriptionRequest);
    }

    private function getVendors(): array
    {
        if (empty($this->vendors)) {
            $this->vendors = $this->vendorProvider->findAll();
        }

        return $this->vendors;
    }

    public function getDependencies()
    {
        return [
            CustomerFixtures::class,
            VendorFixtures::class,
            VendorPlanFixtures::class,
        ];
    }
}
