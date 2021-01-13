<?php

namespace App\Subscription\Dto;

use App\Customer\Dto\CustomerDto;
use App\Vendor\Dto\VendorPlanDto;

class SubscriptionDto
{
    public string $id;

    public ?string $customerId;

    public ?CustomerDto $customer;

    public ?string $vendorPlanId;

    public ?VendorPlanDto $vendorPlan;

    public ?string $expiresAt;

    public ?string $reviewedAt;

    public ?bool $isApproved;

    public ?bool $isRecurring;
}
