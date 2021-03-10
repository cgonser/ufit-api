<?php

namespace App\Payment\Dto;

use App\Core\Dto\CurrencyDto;
use App\Core\Dto\PaymentMethodDto;

class PaymentDto
{
    public ?string $id;

    public ?string $customerId;

    public ?string $vendorId;

    public ?string $subscriptionCycleId;

//    public ?SubscriptionCycleDto $subscriptionCycle;

    public ?string $paymentMethodId;

    public ?PaymentMethodDto $paymentMethod;

    public ?string $currencyId;

    public ?CurrencyDto $currency;

    public ?string $status;

    public ?string $amount;

    public ?string $dueDate;

    public ?string $paidAt;

    public ?string $overdueNotificationSentAt;

    public ?string $createdAt;

    public ?string $updatedAt;

    public ?string $deletedAt;
}
