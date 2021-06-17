<?php

namespace App\Vendor\Service;

use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerInvalidBirthDateException;
use App\Customer\Service\CustomerManager;
use App\Vendor\Request\VendorCustomerRequest;
use Decimal\Decimal;

class VendorCustomerRequestManager
{
    private CustomerManager $customerManager;

    public function __construct(
        CustomerManager $customerManager
    ) {
        $this->customerManager = $customerManager;
    }

    public function updateFromRequest(Customer $customer, VendorCustomerRequest $customerRequest)
    {
        $this->mapFromRequest($customer, $customerRequest);

        $this->customerManager->update($customer);
    }

    public function mapFromRequest(Customer $customer, VendorCustomerRequest $customerRequest)
    {
        if (null !== $customerRequest->name) {
            $customer->setName($customerRequest->name);
        }

        if (null !== $customerRequest->phoneIntlCode) {
            $customer->setPhoneIntlCode($customerRequest->phoneIntlCode);
        }

        if (null !== $customerRequest->phoneAreaCode) {
            $customer->setPhoneAreaCode($customerRequest->phoneAreaCode);
        }

        if (null !== $customerRequest->phoneNumber) {
            $customer->setPhoneNumber($customerRequest->phoneNumber);
        }

        if (null !== $customerRequest->height) {
            $customer->setHeight($customerRequest->height);
        }

        if (null !== $customerRequest->lastWeight) {
            $customer->setLastWeight(new Decimal((string) $customerRequest->lastWeight));
        }

        if (null !== $customerRequest->birthDate) {
            $birthDate = \DateTime::createFromFormat('Y-m-d', $customerRequest->birthDate);

            if (false === $birthDate) {
                throw new CustomerInvalidBirthDateException();
            }

            $customer->setBirthDate($birthDate);
        }

        if (null !== $customerRequest->gender) {
            $customer->setGender($customerRequest->gender);
        }

        if (null !== $customerRequest->goals) {
            $customer->setGoals($customerRequest->goals);
        }
    }
}
