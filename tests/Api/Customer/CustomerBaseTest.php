<?php

namespace App\Tests\Api\Customer;

use App\Customer\Entity\Customer;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class CustomerBaseTest extends WebTestCase
{
    protected const DEFAULT_CUSTOMER_PASSWORD = '123';

    protected ?KernelBrowser $authenticatedClient = null;

    protected ?array $customer = null;

    protected ?ReferenceRepository $fixtures = null;

    protected function createAuthenticatedClient()
    {
        if (null !== $this->authenticatedClient) {
            return $this->authenticatedClient;
        }

        $this->authenticatedClient = $this->createClient();

        $customer = $this->getCustomer();

        $this->jsonPost(
            $this->authenticatedClient,
            'customers/login',
            [
                'username' => $customer->getEmail(),
                'password' => self::DEFAULT_CUSTOMER_PASSWORD,
            ]
        );

        $data = json_decode($this->authenticatedClient->getResponse()->getContent(), true);

        $this->authenticatedClient->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $this->authenticatedClient;
    }

    protected function jsonPost($client, $uri, $data)
    {
        $this->jsonRequest($client, 'POST', $uri, $data);
    }

    protected function jsonPut($client, $uri, $data)
    {
        $this->jsonRequest($client, 'PUT', $uri, $data);
    }

    protected function jsonRequest($client, $method, $uri, $data)
    {
        $client->request(
            $method,
            $uri,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
    }

    protected function getCustomer(): Customer
    {
        if (null === $this->fixtures) {
            $this->fixtures = $this->loadFixtures(
                [
                    'App\Customer\Data0Fixtures\CustomerFixtures',
                ]
            )->getReferenceRepository();
        }

        /** @var Customer $customer */
        $customer = $this->fixtures->getReference('customer1@customer.com');

        return $customer;
    }
}
