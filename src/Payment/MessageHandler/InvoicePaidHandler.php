<?php

declare(strict_types=1);

namespace App\Payment\MessageHandler;

use DateTimeInterface;
use App\Payment\Entity\Invoice;
use App\Payment\Message\InvoicePaidEvent;
use App\Payment\Provider\InvoiceProvider;
use App\Payment\Service\InvoiceManager;
use App\Subscription\Service\SubscriptionManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class InvoicePaidHandler implements MessageHandlerInterface
{
    public function __construct(
        private InvoiceProvider $invoiceProvider,
        private InvoiceManager $invoiceManager,
        private SubscriptionManager $subscriptionManager,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(InvoicePaidEvent $invoicePaidEvent)
    {
        $this->logger->info(
            'invoice.paid',
            [
                'invoiceId' => $invoicePaidEvent->getInvoiceId(),
                'paidAt' => $invoicePaidEvent->getPaidAt()->format(DateTimeInterface::ATOM),
            ]
        );

        /** @var Invoice $invoice */
        $invoice = $this->invoiceProvider->get($invoicePaidEvent->getInvoiceId());
        $this->invoiceManager->markAsPaid($invoice, $invoicePaidEvent->getPaidAt());

        $subscription = $invoice->getSubscription();
        $this->subscriptionManager->approve($subscription, null, $invoice);
    }
}
