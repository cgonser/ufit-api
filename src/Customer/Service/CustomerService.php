<?php

namespace App\Customer\Service;

use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerEmailAddressInUseException;
use App\Customer\Exception\CustomerInvalidBirthDateException;
use App\Customer\Exception\CustomerInvalidPasswordException;
use App\Customer\Provider\CustomerProvider;
use App\Customer\Repository\CustomerRepository;
use App\Customer\Request\CustomerPasswordChangeRequest;
use App\Customer\Request\CustomerRequest;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CustomerService
{
    private UserPasswordEncoderInterface $userPasswordEncoder;

    private CustomerRepository $customerRepository;

    private CustomerProvider $customerProvider;

    public function __construct(
        UserPasswordEncoderInterface $userPasswordEncoder,
        CustomerRepository $customerRepository,
        CustomerProvider $customerProvider
    ) {
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->customerRepository = $customerRepository;
        $this->customerProvider = $customerProvider;
    }

    public function create(CustomerRequest $customerRequest): Customer
    {
        $customer = new Customer();

        $this->mapFromRequest($customer, $customerRequest);

        $this->customerRepository->save($customer);

        return $customer;
    }

    public function update(Customer $customer, CustomerRequest $customerRequest)
    {
        $this->mapFromRequest($customer, $customerRequest);

        $this->customerRepository->save($customer);
    }

    public function mapFromRequest(Customer $customer, CustomerRequest $customerRequest)
    {
        $isEmailAddressInUse = $this->isEmailAddressInUse(
            $customerRequest->email,
            $customer->isNew() ? null : $customer->getId()
        );

        if ($isEmailAddressInUse) {
            throw new CustomerEmailAddressInUseException();
        }

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
            $customer->setPassword($this->userPasswordEncoder->encodePassword($customer, $customerRequest->password));
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

        if ($customer->isNew() || 0 == count($customer->getRoles())) {
            $customer->setRoles(['ROLE_CUSTOMER']);
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

    public function isEmailAddressInUse(string $emailAddress, ?UuidInterface $customerId = null): bool
    {
        /** @var Customer $existingCustomer */
        $existingCustomer = $this->customerProvider->findOneByEmail($emailAddress);

        if (null === $existingCustomer) {
            return false;
        }

        if (null !== $customerId && $existingCustomer->getId()->toString() == $customerId->toString()) {
            return false;
        }

        return true;
    }

    public function changePassword(
        Customer $customer,
        CustomerPasswordChangeRequest $customerPasswordChangeRequest
    ) {
        $isPasswordValid = $this->userPasswordEncoder->isPasswordValid(
            $customer,
            $customerPasswordChangeRequest->currentPassword
        );

        if (!$isPasswordValid) {
            throw new CustomerInvalidPasswordException();
        }

        $customer->setPassword(
            $this->userPasswordEncoder->encodePassword($customer, $customerPasswordChangeRequest->newPassword)
        );

        $this->customerRepository->save($customer);
    }
}
