<?php

namespace App\Vendor\Service;

use App\Core\Validation\EntityValidator;
use App\Vendor\Entity\VendorBankAccount;
//use App\Vendor\Message\VendorBankAccountCreatedEvent;
//use App\Vendor\Message\VendorBankAccountDeletedEvent;
//use App\Vendor\Message\VendorBankAccountUpdatedEvent;
use App\Vendor\Message\VendorBankAccountCreatedEvent;
use App\Vendor\Message\VendorBankAccountDeletedEvent;
use App\Vendor\Message\VendorBankAccountUpdatedEvent;
use App\Vendor\Repository\VendorBankAccountRepository;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

class VendorBankAccountManager
{
    private VendorBankAccountRepository $vendorBankAccountRepository;

    private EntityValidator $validator;

    private MessageBusInterface $messageBus;

    public function __construct(
        VendorBankAccountRepository $vendorBankAccountRepository,
        EntityValidator $validator,
        MessageBusInterface $messageBus
    ) {
        $this->vendorBankAccountRepository = $vendorBankAccountRepository;
        $this->validator = $validator;
        $this->messageBus = $messageBus;
    }

    public function create(VendorBankAccount $vendorBankAccount): void
    {
        $this->validator->validate($vendorBankAccount);

        $this->vendorBankAccountRepository->save($vendorBankAccount);

        try {
            $this->messageBus->dispatch(
                new VendorBankAccountCreatedEvent($vendorBankAccount->getVendorId(), $vendorBankAccount->getId())
            );
        } catch (HandlerFailedException $e) {
            throw $e->getNestedExceptions()[0];
        }
    }

    public function update(VendorBankAccount $vendorBankAccount): void
    {
        $this->validator->validate($vendorBankAccount);

        $this->vendorBankAccountRepository->save($vendorBankAccount);

        try {
            $this->messageBus->dispatch(
                new VendorBankAccountUpdatedEvent($vendorBankAccount->getVendorId(), $vendorBankAccount->getId())
            );
        } catch (HandlerFailedException $e) {
            throw $e->getNestedExceptions()[0];
        }
    }

    public function delete(VendorBankAccount $vendorBankAccount): void
    {
        $this->vendorBankAccountRepository->delete($vendorBankAccount);

        $this->messageBus->dispatch(
            new VendorBankAccountDeletedEvent($vendorBankAccount->getVendorId(), $vendorBankAccount->getId())
        );
    }

    public function markAsInvalid(VendorBankAccount $vendorBankAccount)
    {
        $vendorBankAccount->setIsValid(false);

        $this->vendorBankAccountRepository->save($vendorBankAccount);
    }

    public function markAsValid(VendorBankAccount $vendorBankAccount)
    {
        $vendorBankAccount->setIsValid(true);

        $this->vendorBankAccountRepository->save($vendorBankAccount);
    }
}
