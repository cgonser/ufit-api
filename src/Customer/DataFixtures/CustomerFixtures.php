<?php

namespace App\Customer\DataFixtures;

use App\Customer\Request\CustomerRequest;
use App\Customer\Service\CustomerRequestManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CustomerFixtures extends Fixture
{
    const CUSTOMER_COUNT = 20;

    const CUSTOMER_DEFAULT_PASSWORD = '123';

    const COUNTRIES = ['BR', 'LU'];

    const LOCALES = [
        'BR' => 'pt_BR',
        'LU' => 'en',
    ];

    const TIMEZONES = [
        'BR' => 'America/Sao_Paulo',
        'LU' => 'Europe/Luxembourg',
    ];

    private CustomerRequestManager $customerRequestManager;

    public function __construct(CustomerRequestManager $customerRequestManager)
    {
        $this->customerRequestManager = $customerRequestManager;
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadCustomers($manager);
    }

    private function loadCustomers(ObjectManager $manager): void
    {
        foreach ($this->getData() as $customerRequest) {
            $customer = $this->customerRequestManager->create($customerRequest);

            $this->addReference('customer-'.$customer->getEmail(), $customer);
        }

        $manager->flush();
    }

    private function getData(): \Iterator
    {
        for ($i = 1; $i <= self::CUSTOMER_COUNT; ++$i) {
            $country = self::COUNTRIES[array_rand(self::COUNTRIES)];
            $locale = self::LOCALES[$country];
            $timezone = self::TIMEZONES[$country];

            $customerRequest = new CustomerRequest();
            $customerRequest->name = 'Customer '.$i;
            $customerRequest->email = 'customer'.$i.'@ufit.io';
            $customerRequest->password = self::CUSTOMER_DEFAULT_PASSWORD;
            $customerRequest->country = $country;
            $customerRequest->locale = $locale;
            $customerRequest->timezone = $timezone;

            yield $customerRequest;
        }
    }
}
