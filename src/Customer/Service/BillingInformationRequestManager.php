<?php

declare(strict_types=1);

namespace App\Customer\Service;

use App\Customer\Entity\BillingInformation;
use App\Customer\Exception\CustomerInvalidBirthDateException;
use App\Customer\Request\BillingInformationRequest;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class BillingInformationRequestManager
{
    private BillingInformationManager $billingInformationManager;

    public function __construct(BillingInformationManager $billingInformationManager)
    {
        $this->billingInformationManager = $billingInformationManager;
    }

    public function createFromRequest(BillingInformationRequest $billingInformationRequest): BillingInformation
    {
        $billingInformation = new BillingInformation();

        $this->mapFromRequest($billingInformation, $billingInformationRequest);

        $this->billingInformationManager->create($billingInformation);

        return $billingInformation;
    }

    public function updateFromRequest(
        BillingInformation $billingInformation,
        BillingInformationRequest $billingInformationRequest
    ): void {
        $this->mapFromRequest($billingInformation, $billingInformationRequest);

        $this->billingInformationManager->update($billingInformation);
    }

    private function mapFromRequest(
        BillingInformation $billingInformation,
        BillingInformationRequest $billingInformationRequest
    ): void {
        if ($billingInformationRequest->has('customerId')) {
            $billingInformation->setCustomerId(
                $billingInformationRequest->customerId instanceof UuidInterface
                    ? $billingInformationRequest->customerId
                    : Uuid::fromString($billingInformationRequest->customerId)
            );
        }

        if ($billingInformationRequest->has('name')) {
            $billingInformation->setName($billingInformationRequest->name);
        }

        if ($billingInformationRequest->has('email')) {
            $billingInformation->setEmail($billingInformationRequest->email);
        }

        if ($billingInformationRequest->has('birthDate')) {
            $birthDate = \DateTime::createFromFormat('Y-m-d', $billingInformationRequest->birthDate);

            if (false === $birthDate) {
                throw new CustomerInvalidBirthDateException();
            }

            $billingInformation->setBirthDate($birthDate);
        }

        if ($billingInformationRequest->has('phoneIntlCode')) {
            $billingInformation->setPhoneIntlCode($billingInformationRequest->phoneIntlCode);
        }

        if ($billingInformationRequest->has('phoneAreaCode')) {
            $billingInformation->setPhoneAreaCode($billingInformationRequest->phoneAreaCode);
        }

        if ($billingInformationRequest->has('phoneNumber')) {
            $billingInformation->setPhoneNumber($billingInformationRequest->phoneNumber);
        }

        if ($billingInformationRequest->has('documentType')) {
            $billingInformation->setDocumentType($billingInformationRequest->documentType);
        }

        if ($billingInformationRequest->has('documentNumber')) {
            $billingInformation->setDocumentNumber($billingInformationRequest->documentNumber);
        }

        if ($billingInformationRequest->has('addressLine1')) {
            $billingInformation->setAddressLine1($billingInformationRequest->addressLine1);
        }

        if ($billingInformationRequest->has('addressLine2')) {
            $billingInformation->setAddressLine2($billingInformationRequest->addressLine2);
        }

        if ($billingInformationRequest->has('addressNumber')) {
            $billingInformation->setAddressNumber($billingInformationRequest->addressNumber);
        }

        if ($billingInformationRequest->has('addressDistrict')) {
            $billingInformation->setAddressDistrict($billingInformationRequest->addressDistrict);
        }

        if ($billingInformationRequest->has('addressCity')) {
            $billingInformation->setAddressCity($billingInformationRequest->addressCity);
        }

        if ($billingInformationRequest->has('addressState')) {
            $billingInformation->setAddressState($billingInformationRequest->addressState);
        }

        if ($billingInformationRequest->has('addressCountry')) {
            $billingInformation->setAddressCountry($billingInformationRequest->addressCountry);
        }

        if ($billingInformationRequest->has('addressZipCode')) {
            $billingInformation->setAddressZipCode($billingInformationRequest->addressZipCode);
        }
    }
}
