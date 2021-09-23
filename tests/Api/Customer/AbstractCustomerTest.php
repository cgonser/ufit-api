<?php

declare(strict_types=1);

namespace App\Tests\Api\Customer;

use App\Customer\Entity\Customer;
use App\Customer\Service\CustomerManager;
use App\Customer\Service\CustomerPasswordManager;
use App\Tests\Api\AbstractApiTest;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

abstract class AbstractCustomerTest extends AbstractApiTest
{
    protected function authenticateClient(KernelBrowser $client, string $username, string $password): void
    {
        $client->jsonRequest('POST', '/customers/login', [
                'username' => $username,
                'password' => $password,
            ]);

        $responseData = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $responseData['token']));
    }

    protected function getCustomerDummyData(): array
    {
        return [
            'name' => 'Test Customer',
            'username' => 'test-customer',
            'password' => '123',
        ];
    }

    protected function createCustomerDummy(?array $customerData = null): Customer
    {
        if (null === $customerData) {
            $customerData = $this->getCustomerDummyData();
        }

        $customer = new Customer();
        $customer->setName($customerData['name']);
        $customer->setUsername($customerData['username']);
        $customer->setPassword(
            static::getContainer()->get(CustomerPasswordManager::class)->encodePassword(
                $customer,
                $customerData['password']
            )
        );

        static::getContainer()->get(CustomerManager::class)->create($customer);

        return $customer;
    }
}
