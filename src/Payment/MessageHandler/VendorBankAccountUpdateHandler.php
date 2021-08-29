<?php

declare(strict_types=1);

namespace App\Payment\MessageHandler;

use App\Payment\Service\PaymentProcessor\VendorInformationManagerInterface;
use App\Vendor\Message\VendorBankAccountCreatedEvent;
use App\Vendor\Message\VendorBankAccountUpdatedEvent;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class VendorBankAccountUpdateHandler implements MessageSubscriberInterface
{
    private VendorInformationManagerInterface $vendorInformationManager;

    public function __construct(VendorInformationManagerInterface $vendorInformationManager)
    {
        $this->vendorInformationManager = $vendorInformationManager;
    }

    public function handleVendorBankAccountCreatedEvent(VendorBankAccountCreatedEvent $event)
    {
        $this->updateVendorPaymentInformation($event->getVendorId());
    }

    public function handleVendorBankAccountUpdatedEvent(VendorBankAccountUpdatedEvent $event)
    {
        $this->updateVendorPaymentInformation($event->getVendorId());
    }

    public static function getHandledMessages(): iterable
    {
        yield VendorBankAccountCreatedEvent::class => [
            'method' => 'handleVendorBankAccountCreatedEvent',
        ];

        yield VendorBankAccountUpdatedEvent::class => [
            'method' => 'handleVendorBankAccountUpdatedEvent',
        ];
    }

    private function updateVendorPaymentInformation(string $vendorId)
    {
        $this->vendorInformationManager->updateVendorInformation(Uuid::fromString($vendorId));
    }
}
