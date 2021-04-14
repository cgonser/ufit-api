<?php

namespace App\Customer\Service;

use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerInvalidBirthDateException;
use App\Customer\Exception\CustomerNotFoundException;
use App\Customer\Provider\CustomerProvider;
use App\Customer\Request\CustomerPasswordChangeRequest;
use App\Customer\Request\CustomerPasswordResetRequest;
use App\Customer\Request\CustomerPasswordResetTokenRequest;
use App\Customer\Request\CustomerRequest;

class CustomerRequestManager
{
    private CustomerManager $customerManager;

    private CustomerProvider $customerProvider;

    private CustomerPasswordManager $customerPasswordManager;

    public function __construct(
        CustomerManager $customerManager,
        CustomerProvider $customerProvider,
        CustomerPasswordManager $customerPasswordManager
    ) {
        $this->customerManager = $customerManager;
        $this->customerProvider = $customerProvider;
        $this->customerPasswordManager = $customerPasswordManager;
    }

    public function createFromRequest(CustomerRequest $customerRequest): Customer
    {
        $customer = new Customer();

        $this->mapFromRequest($customer, $customerRequest);

        $this->customerManager->create($customer);

        return $customer;
    }

    public function updateFromRequest(Customer $customer, CustomerRequest $customerRequest)
    {
        $this->mapFromRequest($customer, $customerRequest);

        $this->customerManager->update($customer);
    }

    public function mapFromRequest(Customer $customer, CustomerRequest $customerRequest)
    {
        if (null !== $customerRequest->name) {
            $customer->setName($customerRequest->name);
        }

        if (null !== $customerRequest->email) {
            $customer->setEmail($customerRequest->email);
        }

        if (null !== $customerRequest->phone) {
            $customer->setPhone($customerRequest->phone);
        }

        if (null !== $customerRequest->password) {
            $customer->setPassword($this->customerPasswordManager->encodePassword($customer, $customerRequest->password));
        }

        if (null !== $customerRequest->height) {
            $customer->setHeight($customerRequest->height);
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

        if (null !== $customerRequest->documents) {
            $customer->setDocuments($customerRequest->documents);
        }

        if (null !== $customerRequest->country) {
            $customer->setCountry($customerRequest->country);
        }

        if (null !== $customerRequest->locale) {
            $customer->setLocale($customerRequest->locale);
        }

        if (null !== $customerRequest->timezone) {
            $customer->setTimezone($customerRequest->timezone);
        }
    }

    public function changePassword(Customer $customer, CustomerPasswordChangeRequest $customerPasswordChangeRequest)
    {
        $this->customerPasswordManager->changePassword(
            $customer,
            $customerPasswordChangeRequest->currentPassword,
            $customerPasswordChangeRequest->newPassword
        );
    }

    public function startPasswordReset(CustomerPasswordResetRequest $customerPasswordResetRequest)
    {
        $customer = $this->customerProvider->findOneByEmail($customerPasswordResetRequest->emailAddress);

        if (!$customer) {
            return;
        }

        $this->customerPasswordManager->startPasswordReset($customer);
    }

    public function concludePasswordReset(CustomerPasswordResetTokenRequest $customerPasswordResetTokenRequest)
    {
        [$emailAddress, $token] = explode('|', base64_decode($customerPasswordResetTokenRequest->token));

        $customer = $this->customerProvider->findOneByEmail($emailAddress);

        if (!$customer) {
            throw new CustomerNotFoundException();
        }

        $this->customerPasswordManager->resetPassword($customer, $token, $customerPasswordResetTokenRequest->password);
    }
}
