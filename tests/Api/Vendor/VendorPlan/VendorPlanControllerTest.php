<?php

declare(strict_types=1);

namespace App\Tests\Api\Vendor\VendorPlan;

use App\Vendor\Entity\VendorPlan;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;

class VendorPlanControllerTest extends AbstractVendorPlanTest
{
    public function testFind(): void
    {
        $client = static::createClient();

        $vendorData = $this->getVendorDummyData();
        $vendor = $this->createVendorDummy($vendorData);

        $vendorPlanData = $this->getVendorPlanDummyData();
        $vendorPlan = $this->createVendorPlanDummy($vendor, $vendorPlanData);

        $client->request('GET', '/vendors/'.$vendor->getId()->toString().'/plans');
        $this->assertJsonResponse(Response::HTTP_OK);

        $responseData = $this->getAndAssertJsonResponseData($client);

        foreach ($responseData as $vendorPlanData) {
            $this->assertVendorPlanResponse($vendorPlan, $vendorPlanData);
        }
    }

    public function testGet(): void
    {
        $client = static::createClient();

        $vendorData = $this->getVendorDummyData();
        $vendor = $this->createVendorDummy($vendorData);

        $vendorPlanData = $this->getVendorPlanDummyData();
        $vendorPlan = $this->createVendorPlanDummy($vendor, $vendorPlanData);

        $client->request('GET', '/vendors/'.$vendor->getId()->toString().'/plans/'.$vendorPlan->getId()->toString());
        $this->assertJsonResponse(Response::HTTP_OK);

        $this->assertVendorPlanResponse($vendorPlan, $this->getAndAssertJsonResponseData($client));
    }

    public function testNotFound(): void
    {
        $client = static::createClient();

        $vendorData = $this->getVendorDummyData();
        $vendor = $this->createVendorDummy($vendorData);

        $client->request('GET', '/vendors/'.$vendor->getId()->toString().'/plans/'.Uuid::uuid4());
        $this->assertJsonResponse(Response::HTTP_NOT_FOUND);
    }

    private function assertVendorPlanResponse(VendorPlan $vendorPlan, array $responseData): void
    {
        $this->assertSame($vendorPlan->getId()->toString(), $responseData['id']);
        $this->assertSame($vendorPlan->getVendorId()->toString(), $responseData['vendorId']);
        $this->assertSame($vendorPlan->getName(), $responseData['name']);
        $this->assertSame($vendorPlan->getDescription(), $responseData['description']);
        $this->assertSame($vendorPlan->getCurrency()->getCode(), $responseData['currency']);
        $this->assertSame($vendorPlan->getDuration()?->d, $responseData['durationDays']);
        $this->assertSame($vendorPlan->getDuration()?->m, $responseData['durationMonths']);
        $this->assertSame($vendorPlan->getPrice()->toFloat(), $responseData['price']);
        $this->assertSame($vendorPlan->isVisible(), $responseData['isVisible']);
        $this->assertSame($vendorPlan->isRecurring(), $responseData['isRecurring']);
        $this->assertSame($vendorPlan->isActive(), $responseData['isActive']);
    }
}
