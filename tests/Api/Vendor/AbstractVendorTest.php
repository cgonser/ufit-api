<?php

declare(strict_types=1);

namespace App\Tests\Api\Vendor;

use App\Tests\Api\AbstractApiTest;
use App\Vendor\Entity\Vendor;
use App\Vendor\Service\VendorManager;
use App\Vendor\Service\VendorPasswordManager;
use joshtronic\LoremIpsum;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

abstract class AbstractVendorTest extends AbstractApiTest
{
    protected function authenticateClient(KernelBrowser $client, string $username, string $password): void
    {
        $client->jsonRequest('POST', '/vendors/login', [
                'username' => $username,
                'password' => $password,
            ]);

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $responseData['token']));
    }

    protected function getVendorDummyData(): array
    {
        return [
            'name' => 'Test Vendor',
            'displayName' => 'Test Vendor',
            'email' => 'test-vendor@ufit.io',
            'biography' => (new LoremIpsum())->paragraphs(3),
            'country' => 'BR',
            'locale' => 'pt_BR',
            'timezone' => 'America/Sao_Paulo',
            'allowEmailMarketing' => true,
            'password' => '123',
        ];
    }

    protected function createVendorDummy(?array $vendorData = null): Vendor
    {
        if (null === $vendorData) {
            $vendorData = $this->getVendorDummyData();
        }

        $vendor = new Vendor();

        foreach ($vendorData as $property => $value) {
            if ('password' === $property) {
                $vendor->setPassword(
                    static::getContainer()->get(VendorPasswordManager::class)->encodePassword($vendor, $value)
                );

                continue;
            }

            $vendor->{'set'.ucfirst($property)}($value);
        }

        $vendorManager = static::getContainer()->get(VendorManager::class);

        $vendor->setSlug($vendorManager->generateSlug($vendor));
        $vendorManager->create($vendor);

        return $vendor;
    }
}
