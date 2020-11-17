<?php

namespace App\Customer\Service;

use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerEmailAddressInUseException;
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

        $customer->setName($customerRequest->name);
        $customer->setEmail($customerRequest->email);

        if (null !== $customerRequest->password) {
            $customer->setPassword($this->userPasswordEncoder->encodePassword($customer, $customerRequest->password));
        }

        if ($customer->isNew() || 0 == count($customer->getRoles())) {
            $customer->setRoles(['ROLE_CUSTOMER']);
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
