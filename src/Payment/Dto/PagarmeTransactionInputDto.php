<?php

namespace App\Payment\Dto;

use Decimal\Decimal;

class PagarmeTransactionInputDto
{
    public string $customerId;

    public string $customerName;

    public string $customerEmail;

    public ?string $customerPhone;

    public string $customerDocumentType;

    public string $customerDocumentNumber;

    public Decimal $amount;

    public ?string $cardHash = null;

    public string $productId;

    public string $productName;
}