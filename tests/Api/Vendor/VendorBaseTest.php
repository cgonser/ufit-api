<?php

namespace App\Tests\Api\Vendor;

use App\Vendor\Entity\Vendor;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class VendorBaseTest extends WebTestCase
{
    protected const DEFAULT_VENDOR_PASSWORD = '123';

    protected ?KernelBrowser $authenticatedClient = null;

    protected ?array $vendor = null;

    protected ?ReferenceRepository $fixtures = null;

    protected function createAuthenticatedClient()
    {
        if (null !== $this->authenticatedClient) {
            return $this->authenticatedClient;
        }

        $this->authenticatedClient = $this->createClient();

        $vendor = $this->getVendor();

        $this->jsonPost(
            $this->authenticatedClient,
            'vendors/login',
            [
                'username' => $vendor->getEmail(),
                'password' => self::DEFAULT_VENDOR_PASSWORD,
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

    protected function getVendor(): Vendor
    {
        $vendor = (new Vendor())
            ->setName('Test Vendor 1')
            ->setEmail('test-1@ufit.io')
            ->setPassword(self::DEFAULT_VENDOR_PASSWORD);

        return $vendor;
    }
}
