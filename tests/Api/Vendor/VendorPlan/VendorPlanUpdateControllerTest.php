<?php

declare(strict_types=1);

namespace App\Tests\Api\Vendor\VendorPlan;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VendorPlanUpdateControllerTest extends AbstractVendorPlanTest
{
    public function testPatch(): void
    {
        $this->testUpdate(Request::METHOD_PATCH);
    }

    public function testPut(): void
    {
        $this->testUpdate(Request::METHOD_PUT);
    }

    private function testUpdate(string $requestMethod): void
    {
        $client = static::createClient();
        $vendorData = $this->getVendorDummyData();
        $vendor = $this->createVendorDummy($vendorData);
        $vendorPlanData = $this->getVendorPlanDummyData();
        $vendorPlan = $this->createVendorPlanDummy($vendor, $vendorPlanData);

        $vendorPlanData['name'] = 'new plan '.time();

        $client->request(
            $requestMethod,
            '/vendors/'.$vendor->getId()->toString().'/plans/'.$vendorPlan->getId()->toString(),
            $vendorPlanData
        );
        $this->assertJsonResponse(Response::HTTP_UNAUTHORIZED);

        $this->authenticateClient($client, $vendorData['email'], $vendorData['password']);
        $client->jsonRequest(
            'PATCH',
            '/vendors/'.$vendor->getId()->toString().'/plans/'.$vendorPlan->getId()->toString(),
            $vendorPlanData
        );
        $this->assertJsonResponse(Response::HTTP_OK);

        $responseData = $this->getAndAssertJsonResponseData($client);

        foreach ($vendorPlanData as $property => $value) {
            $this->assertSame($value, $responseData[$property]);
        }
    }
}
