<?php

declare(strict_types=1);

namespace App\Customer\Service;

use App\Core\Validation\EntityValidator;
use App\Customer\Entity\Customer;
use App\Customer\Message\CustomerCreatedEvent;
use App\Customer\Repository\CustomerRepository;
use Symfony\Component\Messenger\MessageBusInterface;

class CustomerManager
{
    private CustomerRepository $customerRepository;

    private EntityValidator $validator;

    private MessageBusInterface $messageBus;

    public function __construct(
        CustomerRepository $customerRepository,
        EntityValidator $validator,
        MessageBusInterface $messageBus
    ) {
        $this->customerRepository = $customerRepository;
        $this->validator = $validator;
        $this->messageBus = $messageBus;
    }

    public function create(Customer $customer): void
    {
        if (0 === count($customer->getRoles())) {
            $customer->setRoles(['ROLE_CUSTOMER']);
        }

        $this->validator->validate($customer);

        $this->customerRepository->save($customer);

        $this->messageBus->dispatch(new CustomerCreatedEvent($customer->getId()));
    }

    public function update(Customer $customer): void
    {
        $this->validator->validate($customer);

        $this->customerRepository->save($customer);
    }
}
