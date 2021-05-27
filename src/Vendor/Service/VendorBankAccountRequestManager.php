<?php

namespace App\Vendor\Service;

use App\Vendor\Entity\VendorBankAccount;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Request\VendorBankAccountRequest;
use Ramsey\Uuid\Uuid;

class VendorBankAccountRequestManager
{
    private VendorBankAccountManager $vendorBankAccountManager;

    private VendorProvider $vendorProvider;

    public function __construct(
        VendorBankAccountManager $vendorBankAccountManager
    ) {
        $this->vendorBankAccountManager = $vendorBankAccountManager;
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
    ) {
        $this->mapFromRequest($vendorBankAccount, $vendorBankAccountRequest);

        $this->vendorBankAccountManager->update($vendorBankAccount);
    }

    private function mapFromRequest(
        VendorBankAccount $vendorBankAccount,
        VendorBankAccountRequest $vendorBankAccountRequest
    ) {
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
