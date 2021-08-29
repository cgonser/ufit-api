<?php

declare(strict_types=1);

namespace App\Tests\Api\Vendor\VendorPlan;

use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\VendorEmailAddressInUseException;
use joshtronic\LoremIpsum;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

class VendorPlanCreateControllerTest extends AbstractVendorPlanTest
{
    public function testCreate(): void
    {
        $client = static::createClient();
        $vendorData = $this->getVendorDummyData();
        $vendor = $this->createVendorDummy($vendorData);
        $vendorPlanData = $this->getVendorPlanDummyData();

        $client->jsonRequest('POST', '/vendors/'.$vendor->getId()->toString().'/plans', $vendorPlanData);
        $this->assertJsonResponse(Response::HTTP_UNAUTHORIZED);

        $this->authenticateClient($client, $vendorData['email'], $vendorData['password']);
        $client->jsonRequest('POST', '/vendors/'.$vendor->getId()->toString().'/plans', $vendorPlanData);
        $this->assertJsonResponse(Response::HTTP_CREATED);

        $responseData = $this->getAndAssertJsonResponseData($client);

        foreach ($vendorPlanData as $property => $value) {
            $this->assertEquals($value, $responseData[$property]);
        }

        $client->request('GET', '/vendors/'.$vendor->getId()->toString().'/plans/'.$responseData['id']);
        $this->assertJsonResponse(Response::HTTP_OK);

        foreach ($vendorPlanData as $property => $value) {
            $this->assertEquals($value, $responseData[$property]);
        }
    }
}
