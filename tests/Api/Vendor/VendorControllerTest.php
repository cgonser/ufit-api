<?php

declare(strict_types=1);

namespace App\Tests\Api\Vendor;

use App\Vendor\Entity\Vendor;
use joshtronic\LoremIpsum;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

class VendorControllerTest extends AbstractVendorTest
{
    public function testGetPublicVendor(): void
    {
        $client = static::createClient();

        $vendorData = $this->getVendorDummyData();
        $vendor = $this->createVendorDummy($vendorData);

        $this->testGetPublicVendorById($client, $vendor);
        $this->testGetPublicVendorBySlug($client, $vendor);
    }

    private function testGetPublicVendorById(KernelBrowser $client, Vendor $vendor): void
    {
        $client->request('GET', '/vendors/'.$vendor->getId()->toString());
        $this->assertJsonResponse(Response::HTTP_OK);
        $this->testVendorPublicResponseData($vendor, $this->getAndAssertJsonResponseData($client));
    }

    private function testGetPublicVendorBySlug(KernelBrowser $client, Vendor $vendor): void
    {
        $client->request('GET', '/vendors/'.$vendor->getSlug());
        $this->assertJsonResponse(Response::HTTP_OK);
        $this->testVendorPublicResponseData($vendor, $this->getAndAssertJsonResponseData($client));
    }

    private function testVendorPublicResponseData(Vendor $vendor, array $responseData): void
    {
        $this->assertNull($responseData['email']);
        $this->assertNull($responseData['locale']);
        $this->assertNull($responseData['timezone']);
        $this->assertNull($responseData['allowEmailMarketing']);
        $this->assertSame($vendor->getId()->toString(), $responseData['id']);
        $this->assertSame($vendor->getDisplayName(), $responseData['displayName']);
        $this->assertSame($vendor->getName(), $responseData['name']);
        $this->assertSame($vendor->getSlug(), $responseData['slug']);
        $this->assertSame($vendor->getBiography(), $responseData['biography']);
        $this->assertSame($vendor->getCountry(), $responseData['country']);
    }

    public function testGetPrivateVendor(): void
    {
        $client = static::createClient();

        $vendorData = $this->getVendorDummyData();
        $vendor = $this->createVendorDummy($vendorData);

        $client->request('GET', '/vendors/current');
        $this->assertJsonResponse(Response::HTTP_UNAUTHORIZED);

        $this->authenticateClient($client, $vendorData['email'], $vendorData['password']);

        $this->testGetPrivateVendorById($client, $vendor);
        $this->testGetPrivateVendorByCurrent($client, $vendor);
        $this->testGetPrivateVendorBySlug($client, $vendor);
    }

    private function testGetPrivateVendorById(KernelBrowser $client, Vendor $vendor): void
    {
        $client->request('GET', '/vendors/'.$vendor->getId()->toString());
        $this->assertJsonResponse(Response::HTTP_OK);
        $this->testVendorPrivateResponseData($vendor, $this->getAndAssertJsonResponseData($client));
    }

    private function testGetPrivateVendorByCurrent(KernelBrowser $client, Vendor $vendor): void
    {
        $client->request('GET', '/vendors/current');
        $this->assertJsonResponse(Response::HTTP_OK);
        $this->testVendorPrivateResponseData($vendor, $this->getAndAssertJsonResponseData($client));
    }

    private function testGetPrivateVendorBySlug(KernelBrowser $client, Vendor $vendor): void
    {
        $client->request('GET', '/vendors/'.$vendor->getSlug());
        $this->assertJsonResponse(Response::HTTP_OK);
        $this->testVendorPrivateResponseData($vendor, $this->getAndAssertJsonResponseData($client));
    }

    private function testVendorPrivateResponseData(Vendor $vendor, array $responseData): void
    {
        $this->assertSame($vendor->getId()->toString(), $responseData['id']);
        $this->assertSame($vendor->getEmail(), $responseData['email']);
        $this->assertSame($vendor->getDisplayName(), $responseData['displayName']);
        $this->assertSame($vendor->getName(), $responseData['name']);
        $this->assertSame($vendor->getSlug(), $responseData['slug']);
        $this->assertSame($vendor->getBiography(), $responseData['biography']);
        $this->assertSame($vendor->getCountry(), $responseData['country']);
        $this->assertSame($vendor->getTimezone(), $responseData['timezone']);
        $this->assertSame($vendor->getLocale(), $responseData['locale']);
        $this->assertSame($vendor->allowEmailMarketing(), $responseData['allowEmailMarketing']);
    }

    public function testGetAnotherVendor(): void
    {
        $client = static::createClient();

        $vendorData = $this->getVendorDummyData();
        $this->createVendorDummy($vendorData);

        $vendor2Data = $this->getVendorDummyData();
        $vendor2Data['email'] = 'vendor-'.(new LoremIpsum())->word().'@ufit.io';
        $vendor2 = $this->createVendorDummy($vendor2Data);

        $this->authenticateClient($client, $vendorData['email'], $vendorData['password']);

        $this->testGetPublicVendorById($client, $vendor2);
        $this->testGetPublicVendorBySlug($client, $vendor2);
    }

    public function testVendorNotFound(): void
    {
        $client = static::createClient();

        $client->request('GET', '/vendors/some-random-vendor');
        $this->assertJsonResponse(Response::HTTP_NOT_FOUND);
    }
}
