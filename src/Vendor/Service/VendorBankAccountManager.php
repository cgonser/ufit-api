<?php

namespace App\Vendor\Service;

use App\Vendor\Entity\VendorBankAccount;

//use App\Vendor\Message\VendorBankAccountCreatedEvent;
//use App\Vendor\Message\VendorBankAccountDeletedEvent;
//use App\Vendor\Message\VendorBankAccountUpdatedEvent;
use App\Vendor\Message\VendorBankAccountCreatedEvent;
use App\Vendor\Message\VendorBankAccountDeletedEvent;
use App\Vendor\Message\VendorBankAccountUpdatedEvent;
use App\Vendor\Repository\VendorBankAccountRepository;
use Symfony\Component\Messenger\MessageBusInterface;

class VendorBankAccountManager
{
    private VendorBankAccountRepository $vendorBankAccountRepository;

    private MessageBusInterface $messageBus;

    public function __construct(
        VendorBankAccountRepository $vendorBankAccountRepository,
        MessageBusInterface $messageBus
    ) {
        $this->vendorBankAccountRepository = $vendorBankAccountRepository;
        $this->messageBus = $messageBus;
    }

    public function create(VendorBankAccount $vendorBankAccount)
    {
        $this->vendorBankAccountRepository->save($vendorBankAccount);

        $this->messageBus->dispatch(
            new VendorBankAccountCreatedEvent($vendorBankAccount->getVendor()->getId(), $vendorBankAccount->getId())
        );
    }

    public function update(VendorBankAccount $vendorBankAccount)
    {
        $this->vendorBankAccountRepository->save($vendorBankAccount);

        $this->messageBus->dispatch(
            new VendorBankAccountUpdatedEvent($vendorBankAccount->getVendor()->getId(), $vendorBankAccount->getId())
        );
    }

    public function delete(VendorBankAccount $vendorBankAccount)
    {
        $this->vendorBankAccountRepository->delete($vendorBankAccount);

        $this->messageBus->dispatch(
            new VendorBankAccountDeletedEvent($vendorBankAccount->getVendor()->getId(), $vendorBankAccount->getId())
        );
    }
}
