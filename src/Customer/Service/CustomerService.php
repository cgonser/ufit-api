<?php

namespace App\Customer\Service;

use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerEmailAddressInUseException;
use App\Customer\Exception\CustomerInvalidPasswordException;
use App\Customer\Provider\CustomerProvider;
use App\Customer\Repository\CustomerRepository;
use App\Customer\Request\CustomerCreateRequest;
use App\Customer\Request\CustomerPasswordChangeRequest;
use App\Customer\Request\CustomerUpdateRequest;
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

    public function create(CustomerCreateRequest $customerCreateRequest): Customer
    {
        if ($this->isEmailAddressInUse($customerCreateRequest->email)) {
            throw new CustomerEmailAddressInUseException();
        }

        $customer = new Customer();
        $customer->setName($customerCreateRequest->name);
        $customer->setEmail($customerCreateRequest->email);
        $customer->setPassword($this->userPasswordEncoder->encodePassword($customer, $customerCreateRequest->password));
        $customer->setRoles(['ROLE_CUSTOMER']);

        $this->customerRepository->save($customer);

        return $customer;
    }

    public function update(Customer $customer, CustomerUpdateRequest $customerUpdateRequest)
    {
        if ($this->isEmailAddressInUse($customerUpdateRequest->email, $customer->getId())) {
            throw new CustomerEmailAddressInUseException();
        }

        $customer->setName($customerUpdateRequest->name);
        $customer->setEmail($customerUpdateRequest->email);

        $this->customerRepository->save($customer);
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
