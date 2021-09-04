<?php

declare(strict_types=1);

namespace App\Tests\Api\Vendor;

use joshtronic\LoremIpsum;
use Symfony\Component\HttpFoundation\Response;

class VendorUpdateControllerTest extends AbstractVendorTest
{
    public function testPatchCurrentVendor(): void
    {
        $client = static::createClient();

        $vendorData = $this->getVendorDummyData();
        $vendor = $this->createVendorDummy($vendorData);

        $requestData = [
            'name' => 'John Doe'.time(),
        ];

        $client->jsonRequest('PATCH', '/vendors/current', $requestData);
        static::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        $this->authenticateClient($client, $vendorData['email'], $vendorData['password']);

        $client->jsonRequest('PATCH', '/vendors/current', $requestData);
        $this->assertJsonResponse(Response::HTTP_OK);
        $responseData = $this->getAndAssertJsonResponseData($client);

        $this->assertSame($requestData['name'], $responseData['name']);
    }

    public function testPatchVendorById(): void
    {
        $client = static::createClient();

        $vendorData = $this->getVendorDummyData();
        $vendor = $this->createVendorDummy($vendorData);

        $requestData = [
            'name' => 'John Doe'.time(),
        ];

        $client->jsonRequest('PATCH', '/vendors/'.$vendor->getId()->toString(), $requestData);
        static::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        $this->authenticateClient($client, $vendorData['email'], $vendorData['password']);

        $client->jsonRequest('PATCH', '/vendors/'.$vendor->getId()->toString(), $requestData);
        $this->assertJsonResponse(Response::HTTP_OK);
        $responseData = $this->getAndAssertJsonResponseData($client);

        $this->assertSame($requestData['name'], $responseData['name']);
    }

    public function testPatchAnotherVendor(): void
    {
        $client = static::createClient();

        $vendorData = $this->getVendorDummyData();
        $this->createVendorDummy($vendorData);

        $vendor2Data = $this->getVendorDummyData();
        $vendor2Data['email'] = 'vendor-'.(new LoremIpsum())->word().'@ufit.io';
        $vendor2 = $this->createVendorDummy($vendor2Data);

        $requestData = [
            'name' => 'John Doe'.time(),
        ];

        $this->authenticateClient($client, $vendorData['email'], $vendorData['password']);

        $client->jsonRequest('PATCH', '/vendors/'.$vendor2->getId()->toString(), $requestData);
        static::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testPutVendor(): void
    {
        $client = static::createClient();

        $vendorData = $this->getVendorDummyData();
        $this->createVendorDummy($vendorData);
        $this->authenticateClient($client, $vendorData['email'], $vendorData['password']);

        $vendorData['name'] = 'John Doe'.time();

        $client->jsonRequest('PUT', '/vendors/current', $vendorData);
        $this->assertJsonResponse(Response::HTTP_OK);
        $responseData = $this->getAndAssertJsonResponseData($client);

        foreach ($vendorData as $property => $value) {
            if ('password' === $property) {
                continue;
            }

            $this->assertSame($value, $responseData[$property]);
        }
    }
}
