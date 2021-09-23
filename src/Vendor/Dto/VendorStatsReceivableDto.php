<?php

declare(strict_types=1);

namespace App\Vendor\Dto;

class VendorStatsReceivableDto
{
    public ?string $currentAmount = null;

    public ?string $nextPaymentAmount = null;

    public ?string $nextPaymentDate = null;
}
