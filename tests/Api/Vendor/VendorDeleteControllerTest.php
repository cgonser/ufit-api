<?php

declare(strict_types=1);

namespace App\Tests\Api\Vendor;

use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\VendorNotFoundException;
use App\Vendor\Provider\VendorProvider;
use joshtronic\LoremIpsum;
use Symfony\Component\HttpFoundation\Response;

class VendorDeleteControllerTest extends AbstractVendorTest
{
    public function testDeleteCurrentVendor(): void
    {
        $this->markTestSkipped('Vendor DELETE not implemented yet');

        $client = static::createClient();

        $vendorData = $this->getVendorDummyData();
        $vendor = $this->createVendorDummy($vendorData);

        $client->jsonRequest('DELETE', '/vendors/current');
        static::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        $this->authenticateClient($client, $vendorData['email'], $vendorData['password']);

        $client->jsonRequest('DELETE', '/vendors/current');
        static::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        $this->expectException(VendorNotFoundException::class);
        static::getContainer()->get('doctrine')->getManager()->clear(Vendor::class);
        static::getContainer()->get(VendorProvider::class)->get($vendor->getId());
    }

    public function testDeleteAnotherVendor(): void
    {
        $this->markTestSkipped('Vendor DELETE not implemented yet');

        $client = static::createClient();

        $vendorData = $this->getVendorDummyData();
        $this->createVendorDummy($vendorData);

        $vendor2Data = $this->getVendorDummyData();
        $vendor2Data['email'] = 'vendor-'.(new LoremIpsum())->word().'@ufit.io';
        $vendor2 = $this->createVendorDummy($vendor2Data);

        $client->jsonRequest('DELETE', '/vendors/'.$vendor2->getId()->toString());
        static::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $this->authenticateClient($client, $vendorData['email'], $vendorData['password']);

        $client->jsonRequest('DELETE', '/vendors/'.$vendor2->getId()->toString());
        static::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
