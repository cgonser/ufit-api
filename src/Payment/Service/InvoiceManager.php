<?php

namespace App\Payment\Service;

use App\Payment\Entity\Invoice;
use App\Payment\Repository\InvoiceRepository;
use App\Subscription\Entity\Subscription;
use Symfony\Component\Messenger\MessageBusInterface;

class InvoiceManager
{
    private InvoiceRepository $invoiceRepository;

    private MessageBusInterface $messageBus;

    public function __construct(
        InvoiceRepository $invoiceRepository,
        MessageBusInterface $messageBus
    ) {
        $this->messageBus = $messageBus;
        $this->invoiceRepository = $invoiceRepository;
    }

    public function createFromSubscription(Subscription $subscription): Invoice
    {
        $invoice = new Invoice();
        $invoice->setSubscription($subscription);
        $invoice->setCurrency($subscription->getVendorPlan()->getCurrency());
        $invoice->setTotalAmount($subscription->getPrice());
        $invoice->setDueDate(new \DateTime());

        $this->invoiceRepository->save($invoice);

        return $invoice;
    }
}
