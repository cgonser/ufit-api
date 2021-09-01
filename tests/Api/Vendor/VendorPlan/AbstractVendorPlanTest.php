<?php

declare(strict_types=1);

namespace App\Tests\Api\Vendor\VendorPlan;

use App\Tests\Api\Vendor\AbstractVendorTest;
use App\Vendor\Entity\Vendor;
use App\Vendor\Entity\VendorPlan;
use App\Vendor\Request\VendorPlanRequest;
use App\Vendor\Service\VendorPlanRequestManager;
use joshtronic\LoremIpsum;

abstract class AbstractVendorPlanTest extends AbstractVendorTest
{
    protected function getVendorPlanDummyData(): array
    {
        $loremIpsum = new LoremIpsum();

        //todo: add questionnaire
        return [
            'name' => $loremIpsum->words(2),
            'description' => $loremIpsum->paragraphs(3),
            'currency' => 'BRL',
            'price' => '100',
            'durationDays' => 0,
            'durationMonths' => 1,
            //            'isApprovalRequired' => false,
            'isRecurring' => true,
            'isVisible' => true,
        ];
    }

    protected function createVendorPlanDummy(Vendor $vendor, ?array $data = null): VendorPlan
    {
        if (null === $data) {
            $data = $this->getVendorPlanDummyData();
        }

        $vendorPlanRequest = new VendorPlanRequest();

        foreach ($data as $property => $value) {
            $vendorPlanRequest->{$property} = $value;
        }

        return self::getContainer()->get(VendorPlanRequestManager::class)->createFromRequest(
            $vendor,
            $vendorPlanRequest
        );
    }
}
