<?php

declare(strict_types=1);

namespace App\Payment\MessageHandler;

use App\Payment\Entity\Invoice;
use App\Payment\Message\InvoicePaidEvent;
use App\Payment\Provider\InvoiceProvider;
use App\Payment\Service\InvoiceManager;
use App\Subscription\Service\SubscriptionManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class InvoicePaidHandler implements MessageHandlerInterface
{
    private InvoiceManager $invoiceManager;

    private InvoiceProvider $invoiceProvider;

    private SubscriptionManager $subscriptionManager;

    private LoggerInterface $logger;

    public function __construct(
        InvoiceProvider $invoiceProvider,
        InvoiceManager $invoiceManager,
        SubscriptionManager $subscriptionManager,
        LoggerInterface $logger
    ) {
        $this->invoiceManager = $invoiceManager;
        $this->invoiceProvider = $invoiceProvider;
        $this->subscriptionManager = $subscriptionManager;
        $this->logger = $logger;
    }

    public function __invoke(InvoicePaidEvent $event)
    {
        $this->logger->info(
            'invoice.paid',
            [
                'invoiceId' => $event->getInvoiceId(),
                'paidAt' => $event->getPaidAt()
                    ->format(\DateTimeInterface::ATOM),
            ]
        );

        /** @var Invoice $invoice */
        $invoice = $this->invoiceProvider->get($event->getInvoiceId());
        $this->invoiceManager->markAsPaid($invoice, $event->getPaidAt());

        $subscription = $invoice->getSubscription();
        $this->subscriptionManager->approve($subscription, null, $invoice);
    }
}
