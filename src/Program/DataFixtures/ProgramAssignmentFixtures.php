<?php

declare(strict_types=1);

namespace App\Program\DataFixtures;

use App\Customer\Entity\Customer;
use App\Program\Provider\VendorProgramProvider;
use App\Program\Request\ProgramAssignmentRequest;
use App\Program\Service\ProgramAssignmentManager;
use App\Subscription\DataFixtures\SubscriptionFixtures;
use App\Subscription\Provider\SubscriptionProvider;
use App\Vendor\Entity\Vendor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProgramAssignmentFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private VendorProgramProvider $vendorProgramProvider, private SubscriptionProvider $subscriptionProvider, private ProgramAssignmentManager $programAssignmentManager)
    {
    }

    public function load(ObjectManager $objectManager): void
    {
        foreach ($this->subscriptionProvider->findAll() as $subscription) {
            $this->assignProgram($subscription->getCustomer(), $subscription->getVendorPlan()->getVendor());
        }

        $objectManager->flush();
    }

    /**
     * @return class-string<ProgramFixtures>[]|class-string<SubscriptionFixtures>[]
     */
    public function getDependencies(): array
    {
        return [ProgramFixtures::class, SubscriptionFixtures::class];
    }

    private function assignProgram(Customer $customer, Vendor $vendor): void
    {
        $programAssignmentRequest = new ProgramAssignmentRequest();
        $programAssignmentRequest->customerId = $customer->getId()
            ->toString();

        $programs = $this->vendorProgramProvider->findByVendor($vendor);
        $program = $programs[array_rand($programs)];

        $this->programAssignmentManager->createFromRequest($program, $programAssignmentRequest);
    }
}
