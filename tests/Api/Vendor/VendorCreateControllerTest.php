<?php

declare(strict_types=1);

namespace App\Tests\Api\Vendor;

use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\VendorEmailAddressInUseException;
use joshtronic\LoremIpsum;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

class VendorCreateControllerTest extends AbstractVendorTest
{
    public function testCreateVendor(): void
    {
        $client = static::createClient();
        $vendorData = $this->getVendorDummyData();

        $client->jsonRequest('POST', '/vendors', $vendorData);
        $this->assertJsonResponse(Response::HTTP_OK);
        $responseData = $this->getAndAssertJsonResponseData($client);

        self::assertNotNull($responseData['token']);
        self::assertNotNull($responseData['refresh_token']);
    }

    public function testCreateDuplicatedVendor(): void
    {
        $client = static::createClient();
        $vendorData = $this->getVendorDummyData();

        $client->jsonRequest('POST', '/vendors', $vendorData);
        $this->assertJsonResponse(Response::HTTP_OK);

        $client->jsonRequest('POST', '/vendors', $vendorData);
        $this->assertJsonResponse(Response::HTTP_BAD_REQUEST);
        $responseData = $this->getAndAssertJsonResponseData($client);

        $this->assertArrayHasKey('errors', $responseData);
        $this->assertNotEmpty(
            array_filter($responseData['errors'], static function ($error) {
                return 'email' === $error['propertyPath'];
            })
        );
    }
}
