<?php

namespace App\Payment\Dto;

use Decimal\Decimal;

class PagarmeTransactionInputDto
{
    public string $vendorId;

    public string $vendorPlanId;

    public ?int $pagarmePlanId;

    public string $subscriptionId;

    public string $paymentId;

    public string $invoiceId;

    public string $customerId;

    public string $customerName;

    public string $customerEmail;

    public ?string $customerPhoneIntlCode;

    public ?string $customerPhoneAreaCode;

    public ?string $customerPhoneNumber;

    public string $customerDocumentType;

    public string $customerDocumentNumber;

    public Decimal $amount;

    public ?string $cardHash = null;

    public string $productId;

    public string $productName;

    public bool $isRecurring = false;
}