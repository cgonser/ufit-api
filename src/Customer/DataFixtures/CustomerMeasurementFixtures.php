<?php

namespace App\Customer\DataFixtures;

use App\Customer\Entity\Customer;
use App\Customer\Provider\CustomerProvider;
use App\Customer\Provider\MeasurementTypeProvider;
use App\Customer\Request\CustomerMeasurementItemRequest;
use App\Customer\Request\CustomerMeasurementRequest;
use App\Customer\Service\CustomerMeasurementService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CustomerMeasurementFixtures extends Fixture implements DependentFixtureInterface
{
    private CustomerMeasurementService $service;

    private CustomerProvider $customerProvider;

    private MeasurementTypeProvider $measurementTypeProvider;

    public function __construct(
        CustomerMeasurementService $service,
        CustomerProvider $customerProvider,
        MeasurementTypeProvider $measurementTypeProvider
    ) {
        $this->service = $service;
        $this->customerProvider = $customerProvider;
        $this->measurementTypeProvider = $measurementTypeProvider;
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
        $customerMeasurementRequest = new CustomerMeasurementRequest();
        $customerMeasurementRequest->takenAt = (new \DateTime())->format(\DateTimeInterface::ISO8601);
        $customerMeasurementRequest->notes = 'Updating my measurements';
        $customerMeasurementRequest->items = [];

        $customerMeasurementItemRequest = new CustomerMeasurementItemRequest();
        $customerMeasurementItemRequest->measurementTypeId = $this->measurementTypeProvider
            ->findOneBySlug('weight')->getId()->toString();
        $customerMeasurementItemRequest->measurement = rand(50, 150);
        $customerMeasurementItemRequest->unit = 'kg';
        $customerMeasurementRequest->items[] = $customerMeasurementItemRequest;

        $customerMeasurementItemRequest = new CustomerMeasurementItemRequest();
        $customerMeasurementItemRequest->measurementTypeId = $this->measurementTypeProvider
            ->findOneBySlug('height')->getId()->toString();
        $customerMeasurementItemRequest->measurement = rand(150, 210);
        $customerMeasurementItemRequest->unit = 'cm';
        $customerMeasurementRequest->items[] = $customerMeasurementItemRequest;

        $this->service->create($customer, $customerMeasurementRequest);
    }

    public function getDependencies()
    {
        return [
            CustomerFixtures::class,
            MeasurementTypeFixtures::class,
        ];
    }
}
