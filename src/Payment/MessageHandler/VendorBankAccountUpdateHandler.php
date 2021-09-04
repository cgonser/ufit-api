<?php

declare(strict_types=1);

namespace App\Payment\MessageHandler;

use Iterator;
use App\Payment\Service\PaymentProcessor\VendorInformationManagerInterface;
use App\Vendor\Message\VendorBankAccountCreatedEvent;
use App\Vendor\Message\VendorBankAccountUpdatedEvent;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class VendorBankAccountUpdateHandler implements MessageSubscriberInterface
{
    public function __construct(private VendorInformationManagerInterface $vendorInformationManager)
    {
    }

    public function handleVendorBankAccountCreatedEvent(
        VendorBankAccountCreatedEvent $vendorBankAccountCreatedEvent
    ): void {
        $this->updateVendorPaymentInformation($vendorBankAccountCreatedEvent->getVendorId());
    }

    public function handleVendorBankAccountUpdatedEvent(
        VendorBankAccountUpdatedEvent $vendorBankAccountUpdatedEvent
    ): void {
        $this->updateVendorPaymentInformation($vendorBankAccountUpdatedEvent->getVendorId());
    }

    /**
     * @return Iterator<array<string, string>>
     */
    public static function getHandledMessages(): iterable
    {
        yield VendorBankAccountCreatedEvent::class => [
            'method' => 'handleVendorBankAccountCreatedEvent',
        ];

        yield VendorBankAccountUpdatedEvent::class => [
            'method' => 'handleVendorBankAccountUpdatedEvent',
        ];
    }

    private function updateVendorPaymentInformation(UuidInterface $vendorId): void
    {
        $this->vendorInformationManager->updateVendorInformation($vendorId);
    }
}
