<?php

declare(strict_types=1);

namespace App\Subscription\DataFixtures;

use App\Customer\DataFixtures\CustomerFixtures;
use App\Customer\Entity\Customer;
use App\Customer\Provider\CustomerProvider;
use App\Subscription\Request\SubscriptionRequest;
use App\Subscription\Service\SubscriptionRequestManager;
use App\Vendor\DataFixtures\VendorFixtures;
use App\Vendor\DataFixtures\VendorPlanFixtures;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorProvider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SubscriptionFixtures extends Fixture implements DependentFixtureInterface
{
    private array $vendors = [];

    public function __construct(private SubscriptionRequestManager $subscriptionRequestManager, private CustomerProvider $customerProvider, private VendorProvider $vendorProvider)
    {
    }

    public function load(ObjectManager $objectManager): void
    {
        foreach ($this->customerProvider->findAll() as $customer) {
            $this->loadCustomer($customer);
        }

        $objectManager->flush();
    }

    /**
     * @return class-string<CustomerFixtures>[]|class-string<VendorFixtures>[]|class-string<VendorPlanFixtures>[]
     */
    public function getDependencies(): array
    {
        return [CustomerFixtures::class, VendorFixtures::class, VendorPlanFixtures::class];
    }

    private function loadCustomer(Customer $customer): void
    {
        $vendors = $this->getVendors();
        /** @var Vendor $vendor */
        $vendor = $vendors[array_rand($vendors)];

        $collection = $vendor->getPlans();
        $vendorPlan = $collection->get(random_int(0, $collection->count() - 1));

        $subscriptionRequest = new SubscriptionRequest();
        $subscriptionRequest->vendorPlanId = $vendorPlan->getId()
            ->toString();

        $this->subscriptionRequestManager->createFromCustomerRequest($customer, $subscriptionRequest);
    }

    /**
     * @return mixed[]
     */
    private function getVendors(): array
    {
        if (empty($this->vendors)) {
            $this->vendors = $this->vendorProvider->findAll();
        }

        return $this->vendors;
    }
}
