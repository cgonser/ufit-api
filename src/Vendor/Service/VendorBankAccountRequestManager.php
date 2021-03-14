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
        VendorBankAccountManager $vendorBankAccountManager,
        VendorProvider $vendorProvider
    ) {
        $this->vendorBankAccountManager = $vendorBankAccountManager;
        $this->vendorProvider = $vendorProvider;
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
        if (null !== $vendorBankAccountRequest->vendorId) {
            $vendor = $this->vendorProvider->get(Uuid::fromString($vendorBankAccountRequest->vendorId));

            $vendorBankAccount->setVendor($vendor);
        }

        if (null !== $vendorBankAccountRequest->bankCode) {
            $vendorBankAccount->setBankCode($vendorBankAccountRequest->bankCode);
        }

        if (null !== $vendorBankAccountRequest->agencyNumber) {
            $vendorBankAccount->setAgencyNumber($vendorBankAccountRequest->agencyNumber);
        }

        if (null !== $vendorBankAccountRequest->accountNumber) {
            $vendorBankAccount->setAccountNumber($vendorBankAccountRequest->accountNumber);
        }

        if (null !== $vendorBankAccountRequest->accountDigit) {
            $vendorBankAccount->setAccountDigit($vendorBankAccountRequest->accountDigit);
        }

        if (null !== $vendorBankAccountRequest->ownerName) {
            $vendorBankAccount->setOwnerName($vendorBankAccountRequest->ownerName);
        }

        if (null !== $vendorBankAccountRequest->ownerDocumentType) {
            $vendorBankAccount->setOwnerDocumentType($vendorBankAccountRequest->ownerDocumentType);
        }

        if (null !== $vendorBankAccountRequest->ownerDocumentNumber) {
            $vendorBankAccount->setOwnerDocumentNumber($vendorBankAccountRequest->ownerDocumentNumber);
        }
    }
}
