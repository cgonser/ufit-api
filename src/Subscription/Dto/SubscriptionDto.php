<?php

namespace App\Subscription\Dto;

class SubscriptionDto
{
    public string $id;

    public string $customerId;

    public string $vendorPlanId;

    public ?string $expiresAt;

    public ?string $reviewedAt;

    public ?bool $isApproved;
}
