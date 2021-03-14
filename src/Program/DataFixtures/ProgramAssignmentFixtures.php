<?php

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
    private VendorProgramProvider $programProvider;

    private SubscriptionProvider $subscriptionProvider;

    private ProgramAssignmentManager $programAssignmentManager;

    public function __construct(
        VendorProgramProvider $programProvider,
        SubscriptionProvider $subscriptionProvider,
        ProgramAssignmentManager $programAssignmentManager
    ) {
        $this->programProvider = $programProvider;
        $this->subscriptionProvider = $subscriptionProvider;
        $this->programAssignmentManager = $programAssignmentManager;
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->subscriptionProvider->findAll() as $subscription) {
            $this->assignProgram($subscription->getCustomer(), $subscription->getVendorPlan()->getVendor());
        }

        $manager->flush();
    }

    private function assignProgram(Customer $customer, Vendor $vendor)
    {
        $programAssignmentRequest = new ProgramAssignmentRequest();
        $programAssignmentRequest->customerId = $customer->getId()->toString();

        $programs = $this->programProvider->findByVendor($vendor);
        $program = $programs[array_rand($programs)];

        $this->programAssignmentManager->createFromRequest($program, $programAssignmentRequest);
    }

    public function getDependencies()
    {
        return [
            ProgramFixtures::class,
            SubscriptionFixtures::class,
        ];
    }
}
