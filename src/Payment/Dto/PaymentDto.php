<?php

namespace App\Payment\Dto;

use App\Core\Dto\CurrencyDto;
use App\Core\Dto\PaymentMethodDto;

class PaymentDto
{
    public ?string $id;

    public ?string $invoiceId;

    public ?string $paymentMethodId;

    public ?PaymentMethodDto $paymentMethod;

    public ?string $status;

    public ?string $amount;

    public ?string $dueDate;

    public ?string $paidAt;

    public ?string $createdAt;

    public ?string $updatedAt;
}
