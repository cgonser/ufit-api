<?php

declare(strict_types=1);

namespace App\Customer\Service;

use App\Core\Exception\InvalidEntityException;
use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerInvalidBirthDateException;
use App\Customer\Exception\CustomerNotFoundException;
use App\Customer\Provider\CustomerProvider;
use App\Customer\Request\CustomerEmailChangeRequest;
use App\Customer\Request\CustomerPasswordChangeRequest;
use App\Customer\Request\CustomerPasswordResetRequest;
use App\Customer\Request\CustomerPasswordResetTokenRequest;
use App\Customer\Request\CustomerRequest;
use Decimal\Decimal;
use GeoIp2\Database\Reader;
use Symfony\Component\Intl\Timezones;

class CustomerRequestManager
{
    private CustomerManager $customerManager;

    private CustomerProvider $customerProvider;

    private CustomerPasswordManager $customerPasswordManager;

    private Reader $geoIpReader;

    public function __construct(
        CustomerManager $customerManager,
        CustomerProvider $customerProvider,
        CustomerPasswordManager $customerPasswordManager,
        Reader $geoIpReader
    ) {
        $this->customerManager = $customerManager;
        $this->customerProvider = $customerProvider;
        $this->customerPasswordManager = $customerPasswordManager;
        $this->geoIpReader = $geoIpReader;
    }

    public function createFromRequest(CustomerRequest $customerRequest, ?string $ipAddress = null): Customer
    {
        $customer = new Customer();

        $this->mapFromRequest($customer, $customerRequest);

        if (null !== $ipAddress) {
            $this->localizeCustomer($customer, $ipAddress);
        }

        try {
            $this->customerManager->create($customer);
        } catch (InvalidEntityException $e) {
            if (1 === count($e->getErrors())
                && isset($e->getErrors()[0]['propertyPath'])
                && 'email' === $e->getErrors()[0]['propertyPath']) {
                $existingCustomer = $this->customerProvider->findOneByEmail($customerRequest->email);

                if (null !== $existingCustomer
                    && null === $existingCustomer->getPassword()
                    && ! $customerRequest->has('password')) {
                    return $existingCustomer;
                }
            }

            throw $e;
        }

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
            $customer->setEmail(strtolower($customerRequest->email));
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

        if (null !== $customerRequest->password && null === $customer->getPassword()) {
            $customer->setPassword(
                $this->customerPasswordManager->hashPassword($customer, $customerRequest->password)
            );
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

        if (! $customer) {
            return;
        }

        $this->customerPasswordManager->startPasswordReset($customer);
    }

    public function concludePasswordReset(CustomerPasswordResetTokenRequest $customerPasswordResetTokenRequest)
    {
        [$emailAddress, $token] = explode('|', base64_decode($customerPasswordResetTokenRequest->token, true));

        $customer = $this->customerProvider->findOneByEmail($emailAddress);

        if (! $customer) {
            throw new CustomerNotFoundException();
        }

        $this->customerPasswordManager->resetPassword($customer, $token, $customerPasswordResetTokenRequest->password);
    }

    public function localizeCustomer(Customer $customer, string $ipAddress)
    {
        try {
            if (null === $customer->getCountry()) {
                $record = $this->geoIpReader->country($ipAddress);

                $customer->setCountry($record->country->isoCode);
            }

            if (null === $customer->getTimezone()) {
                $customer->setTimezone(Timezones::forCountryCode($customer->getCountry())[0]);
            }
        } catch (\Exception $e) {
            // do nothing
        }
    }

    public function changeEmail(Customer $customer, CustomerEmailChangeRequest $customerEmailChangeRequest): void
    {
        $customer->setEmail($customerEmailChangeRequest->email);

        $this->customerManager->update($customer);
    }
}
