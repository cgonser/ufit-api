<?php

declare(strict_types=1);

namespace App\Vendor\Service;

use App\Vendor\Entity\VendorBankAccount;
use App\Vendor\Request\VendorBankAccountRequest;
use Ramsey\Uuid\Uuid;

class VendorBankAccountRequestManager
{
    public function __construct(
        private VendorBankAccountManager $vendorBankAccountManager
    ) {
    }

    public function createFromRequest(VendorBankAccountRequest $vendorBankAccountRequest): VendorBankAccount
    {
        $vendorBankAccount = new VendorBankAccount();

        $this->mapFromRequest($vendorBankAccount, $vendorBankAccountRequest);

        $this->vendorBankAccountManager->create($vendorBankAccount);

        return $vendorBankAccount;
    }

    public function updateFromRequest(
        VendorBankAccount $vendorBankAccount,
        VendorBankAccountRequest $vendorBankAccountRequest
    ): void {
        $this->mapFromRequest($vendorBankAccount, $vendorBankAccountRequest);

        $this->vendorBankAccountManager->update($vendorBankAccount);
    }

    private function mapFromRequest(
        VendorBankAccount $vendorBankAccount,
        VendorBankAccountRequest $vendorBankAccountRequest
    ): void {
        if ($vendorBankAccountRequest->has('vendorId')) {
            $vendorBankAccount->setVendorId(Uuid::fromString($vendorBankAccountRequest->vendorId));
        }

        if ($vendorBankAccountRequest->has('bankCode')) {
            $vendorBankAccount->setBankCode($vendorBankAccountRequest->bankCode);
        }

        if ($vendorBankAccountRequest->has('agencyNumber')) {
            $vendorBankAccount->setAgencyNumber($vendorBankAccountRequest->agencyNumber);
        }

        if ($vendorBankAccountRequest->has('accountNumber')) {
            $vendorBankAccount->setAccountNumber($vendorBankAccountRequest->accountNumber);
        }

        if ($vendorBankAccountRequest->has('accountDigit')) {
            $vendorBankAccount->setAccountDigit($vendorBankAccountRequest->accountDigit);
        }

        if ($vendorBankAccountRequest->has('ownerName')) {
            $vendorBankAccount->setOwnerName($vendorBankAccountRequest->ownerName);
        }

        if ($vendorBankAccountRequest->has('ownerDocumentType')) {
            $vendorBankAccount->setOwnerDocumentType($vendorBankAccountRequest->ownerDocumentType);
        }

        if ($vendorBankAccountRequest->has('ownerDocumentNumber')) {
            $vendorBankAccount->setOwnerDocumentNumber($vendorBankAccountRequest->ownerDocumentNumber);
        }
    }
}
