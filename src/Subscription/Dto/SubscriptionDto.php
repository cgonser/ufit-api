<?php

declare(strict_types=1);

namespace App\Subscription\Dto;

use App\Customer\Dto\CustomerDto;
use App\Vendor\Dto\VendorPlanDto;

class SubscriptionDto
{
    public string $id;

    public ?string $customerId = null;

    public ?CustomerDto $customer = null;

    public ?string $vendorPlanId = null;

    public ?VendorPlanDto $vendorPlan = null;

    public ?string $validFrom = null;

    public ?string $expiresAt = null;

    public ?float $price = null;

    public ?string $reviewedAt = null;

    public ?bool $isActive = null;

    public ?bool $isApproved = null;

    public ?bool $isRecurring = null;

    public ?string $cancelledAt = null;
}
