<?php

namespace App\Customer\DataFixtures;

use App\Customer\Request\CustomerCreateRequest;
use App\Customer\Service\CustomerService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CustomerFixtures extends Fixture
{
    private CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadCustomers($manager);
    }

    private function loadCustomers(ObjectManager $manager): void
    {
        foreach ($this->getCustomerData() as [$name, $password, $email]) {
            $customerCreateRequest = new CustomerCreateRequest();
            $customerCreateRequest->name = $name;
            $customerCreateRequest->email = $email;
            $customerCreateRequest->password = $password;

            $customer = $this->customerService->create($customerCreateRequest);

            $this->addReference($email, $customer);
        }

        $manager->flush();
    }

    private function getCustomerData(): array
    {
        return [
            ['Customer 1', '123', 'customer1@customer.com'],
            ['Customer 2', '123', 'customer2@customer.com'],
            ['Customer 3', '123', 'customer3@customer.com'],
        ];
    }
}
