<?php

declare(strict_types=1);

namespace App\Tests\Api\Vendor\VendorPlan;

use App\Vendor\Entity\VendorPlan;
use App\Vendor\Exception\VendorPlanNotFoundException;
use App\Vendor\Provider\VendorPlanProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VendorPlanDeleteControllerTest extends AbstractVendorPlanTest
{
    public function testDeleteUnauthorized(): void
    {
        $client = static::createClient();
        $vendorData = $this->getVendorDummyData();
        $vendor = $this->createVendorDummy($vendorData);
        $vendorPlanData = $this->getVendorPlanDummyData();
        $vendorPlan = $this->createVendorPlanDummy($vendor, $vendorPlanData);

        $client->request(
            Request::METHOD_DELETE,
            '/vendors/'.$vendor->getId()->toString().'/plans/'.$vendorPlan->getId()->toString(),
        );
        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testDelete(): void
    {
        $client = static::createClient();
        $vendorData = $this->getVendorDummyData();
        $vendor = $this->createVendorDummy($vendorData);
        $vendorPlanData = $this->getVendorPlanDummyData();
        $vendorPlan = $this->createVendorPlanDummy($vendor, $vendorPlanData);

        $this->authenticateClient($client, $vendorData['email'], $vendorData['password']);
        $client->jsonRequest(
            Request::METHOD_DELETE,
            '/vendors/'.$vendor->getId()->toString().'/plans/'.$vendorPlan->getId()->toString(),
        );
        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        $client->request('GET', '/vendors/'.$vendor->getId()->toString().'/plans/'.$vendorPlan->getId()->toString());
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        $this->expectException(VendorPlanNotFoundException::class);
        static::getContainer()->get('doctrine')->getManager()->clear(VendorPlan::class);
        static::getContainer()->get(VendorPlanProvider::class)->get($vendorPlan->getId());
    }
}
