<?php

declare(strict_types=1);

namespace App\Subscription\Dto;

use App\Payment\Dto\InvoiceDto;

class SubscriptionCreateDto
{
    public ?SubscriptionDto $subscription;

    public ?InvoiceDto $invoice;
}
