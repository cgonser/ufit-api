<?php

declare(strict_types=1);

namespace App\Vendor\Dto;

class VendorBankAccountDto
{
    public string $id;

    public ?string $vendorId = null;

    public ?string $bankCode = null;

    public ?string $agencyNumber = null;

    public ?string $accountNumber = null;

    public ?string $accountDigit = null;

    public ?string $ownerName = null;

    public ?string $ownerDocumentType = null;

    public ?string $ownerDocumentNumber = null;
}
