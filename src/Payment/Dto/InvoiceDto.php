<?php

namespace App\Payment\Dto;

use App\Core\Dto\CurrencyDto;
use App\Subscription\Dto\SubscriptionDto;

class InvoiceDto
{
    public ?string $id;

    public ?string $subscriptionId;

    public ?SubscriptionDto $subscription;

    public ?string $currencyId;

    public ?CurrencyDto $currency;

    public ?string $totalAmount;

    public ?string $dueDate;

    public ?string $paidAt;

    public ?string $overdueNotificationSentAt;

    public ?string $createdAt;

    public ?string $updatedAt;
}