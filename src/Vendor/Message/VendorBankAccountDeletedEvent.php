<?php

namespace App\Vendor\Message;

use Ramsey\Uuid\UuidInterface;

class VendorBankAccountDeletedEvent
{
    public const NAME = 'vendor.bank_account.deleted';

    protected ?UuidInterface $vendorId = null;

    protected ?UuidInterface $vendorBankAccountId = null;

    public function __construct(UuidInterface $vendorId, UuidInterface $vendorBankAccountId)
    {
        $this->vendorId = $vendorId;
        $this->vendorBankAccountId = $vendorBankAccountId;
    }

    public function getVendorBankAccountId(): ?UuidInterface
    {
        return $this->vendorBankAccountId;
    }

    public function getVendorId(): ?UuidInterface
    {
        return $this->vendorId;
    }
}
