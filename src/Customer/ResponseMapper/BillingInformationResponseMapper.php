<?php

declare(strict_types=1);

namespace App\Customer\ResponseMapper;

use App\Customer\Dto\BillingInformationDto;
use App\Customer\Entity\BillingInformation;

class BillingInformationResponseMapper
{
    public function map(BillingInformation $billingInformation): BillingInformationDto
    {
        $billingInformationDto = new BillingInformationDto();
        $billingInformationDto->id = $billingInformation->getId()?->toString();
        $billingInformationDto->customerId = $billingInformation->getCustomerId()->toString();
        $billingInformationDto->name = $billingInformation->getName();
        $billingInformationDto->email = $billingInformation->getEmail();
        $billingInformationDto->birthDate = $billingInformation->getBirthDate()?->format(\DateTimeInterface::ATOM);
        $billingInformationDto->phoneIntlCode = $billingInformation->getPhoneIntlCode();
        $billingInformationDto->phoneAreaCode = $billingInformation->getPhoneAreaCode();
        $billingInformationDto->phoneNumber = $billingInformation->getPhoneNumber();
        $billingInformationDto->documentType = $billingInformation->getDocumentType();
        $billingInformationDto->documentNumber = $billingInformation->getDocumentNumber();
        $billingInformationDto->addressLine1 = $billingInformation->getAddressLine1();
        $billingInformationDto->addressLine2 = $billingInformation->getAddressLine2();
        $billingInformationDto->addressNumber = $billingInformation->getAddressNumber();
        $billingInformationDto->addressDistrict = $billingInformation->getAddressDistrict();
        $billingInformationDto->addressCity = $billingInformation->getAddressCity();
        $billingInformationDto->addressState = $billingInformation->getAddressState();
        $billingInformationDto->addressCountry = $billingInformation->getAddressCountry();
        $billingInformationDto->addressZipCode = $billingInformation->getAddressZipCode();

        return $billingInformationDto;
    }

    public function mapMultiple(array $billingInformationEntries): array
    {
        $billingInformationDtos = [];

        foreach ($billingInformationEntries as $billingInformation) {
            $billingInformationDtos[] = $this->map($billingInformation);
        }

        return $billingInformationDtos;
    }
}
