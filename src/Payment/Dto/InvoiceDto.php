<?php

declare(strict_types=1);

namespace App\Payment\Dto;

use App\Localization\Dto\CurrencyDto;
use App\Subscription\Dto\SubscriptionDto;

class InvoiceDto
{
    public ?string $id = null;

    public ?string $subscriptionId = null;

    public ?SubscriptionDto $subscription = null;

    public ?string $currencyId = null;

    public ?CurrencyDto $currency = null;

    public ?string $totalAmount = null;

    public ?string $dueDate = null;

    public ?string $paidAt = null;

    public ?string $overdueNotificationSentAt = null;

    public ?string $createdAt = null;

    public ?string $updatedAt = null;
}
