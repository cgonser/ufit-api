<?php

declare(strict_types=1);

namespace App\Vendor\Service;

use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerInvalidBirthDateException;
use App\Customer\Service\CustomerManager;
use App\Vendor\Request\VendorCustomerRequest;
use DateTime;
use Decimal\Decimal;

class VendorCustomerRequestManager
{
    public function __construct(
        private CustomerManager $customerManager
    ) {
    }

    public function updateFromRequest(Customer $customer, VendorCustomerRequest $vendorCustomerRequest): void
    {
        $this->mapFromRequest($customer, $vendorCustomerRequest);

        $this->customerManager->update($customer);
    }

    public function mapFromRequest(Customer $customer, VendorCustomerRequest $vendorCustomerRequest): void
    {
        if (null !== $vendorCustomerRequest->name) {
            $customer->setName($vendorCustomerRequest->name);
        }

        if (null !== $vendorCustomerRequest->phoneIntlCode) {
            $customer->setPhoneIntlCode($vendorCustomerRequest->phoneIntlCode);
        }

        if (null !== $vendorCustomerRequest->phoneAreaCode) {
            $customer->setPhoneAreaCode($vendorCustomerRequest->phoneAreaCode);
        }

        if (null !== $vendorCustomerRequest->phoneNumber) {
            $customer->setPhoneNumber($vendorCustomerRequest->phoneNumber);
        }

        if (null !== $vendorCustomerRequest->height) {
            $customer->setHeight($vendorCustomerRequest->height);
        }

        if (null !== $vendorCustomerRequest->lastWeight) {
            $customer->setLastWeight(new Decimal((string) $vendorCustomerRequest->lastWeight));
        }

        if (null !== $vendorCustomerRequest->birthDate) {
            $birthDate = DateTime::createFromFormat('Y-m-d', $vendorCustomerRequest->birthDate);

            if (false === $birthDate) {
                throw new CustomerInvalidBirthDateException();
            }

            $customer->setBirthDate($birthDate);
        }

        if (null !== $vendorCustomerRequest->gender) {
            $customer->setGender($vendorCustomerRequest->gender);
        }

        if (null !== $vendorCustomerRequest->goals) {
            $customer->setGoals($vendorCustomerRequest->goals);
        }
    }
}
