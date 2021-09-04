<?php

declare(strict_types=1);

namespace App\Payment\Dto;

use App\Localization\Dto\CurrencyDto;
use App\Vendor\Dto\VendorPlanDto;
use OpenApi\Annotations as OA;

class PaymentDto
{
    public ?string $id = null;

    public ?string $invoiceId = null;

    public ?string $currencyId = null;

    public ?CurrencyDto $currency = null;

    public ?string $paymentMethodId = null;

    public ?PaymentMethodDto $paymentMethod = null;

    public ?string $vendorPlanId = null;

    public ?VendorPlanDto $vendorPlan = null;

    /**
     * @OA\Property(enum={"pending", "paid", "rejected"})
     */
    public ?string $status = null;

    public ?string $amount = null;

    /**
     * @OA\Property(type="object")
     */
    public ?array $details = null;

    public ?string $dueDate = null;

    public ?string $paidAt = null;

    public ?string $createdAt = null;

    public ?string $updatedAt = null;
}
