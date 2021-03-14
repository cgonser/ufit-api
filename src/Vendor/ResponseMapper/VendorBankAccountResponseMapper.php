<?php

namespace App\Vendor\ResponseMapper;

use App\Core\ResponseMapper\PaymentMethodResponseMapper;
use App\Vendor\Dto\VendorBankAccountDto;
use App\Vendor\Entity\VendorBankAccount;
use Aws\S3\S3Client;

class VendorBankAccountResponseMapper
{
    public function map(VendorBankAccount $vendorBankAccount): VendorBankAccountDto
    {
        $vendorBankAccountDto = new VendorBankAccountDto();
        $vendorBankAccountDto->id = $vendorBankAccount->getId()->toString();
        $vendorBankAccountDto->vendorId = $vendorBankAccount->getVendor()->getId()->toString();
        $vendorBankAccountDto->bankCode =  $vendorBankAccount->getBankCode();
        $vendorBankAccountDto->agencyNumber =  $vendorBankAccount->getAgencyNumber();
        $vendorBankAccountDto->accountNumber =  $vendorBankAccount->getAccountNumber();
        $vendorBankAccountDto->accountDigit =  $vendorBankAccount->getAccountDigit();
        $vendorBankAccountDto->ownerName =  $vendorBankAccount->getOwnerName();
        $vendorBankAccountDto->ownerDocumentType =  $vendorBankAccount->getOwnerDocumentType();
        $vendorBankAccountDto->ownerDocumentNumber =  $vendorBankAccount->getOwnerDocumentNumber();
        
        return $vendorBankAccountDto;
    }

    public function mapMultiple(array $vendorBankAccounts): array
    {
        $vendorBankAccountDtos = [];

        foreach ($vendorBankAccounts as $vendorBankAccount) {
            $vendorBankAccountDtos[] = $this->map($vendorBankAccount);
        }

        return $vendorBankAccountDtos;
    }
}
