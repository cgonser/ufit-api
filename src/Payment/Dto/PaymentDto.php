<?php

namespace App\Payment\Dto;

use App\Core\Dto\CurrencyDto;
use App\Core\Dto\PaymentMethodDto;
use OpenApi\Annotations as OA;

class PaymentDto
{
    public ?string $id;

    public ?string $invoiceId;

    public ?string $paymentMethodId;

    public ?PaymentMethodDto $paymentMethod;

    /**
     * @OA\Property(enum={"pending", "paid", "rejected"})
     */
    public ?string $status;

    public ?string $amount;

    /**
     * @OA\Property(type="object")
     */
    public ?array $details;

    public ?string $dueDate;

    public ?string $paidAt;

    public ?string $createdAt;

    public ?string $updatedAt;
}
