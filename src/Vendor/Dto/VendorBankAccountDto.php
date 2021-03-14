<?php

namespace App\Vendor\Dto;

class VendorBankAccountDto
{
    public string $id;

    public ?string $vendorId;

    public ?string $bankCode;

    public ?string $agencyNumber;

    public ?string $accountNumber;

    public ?string $accountDigit;

    public ?string $ownerName;

    public ?string $ownerDocumentType;

    public ?string $ownerDocumentNumber;
}
