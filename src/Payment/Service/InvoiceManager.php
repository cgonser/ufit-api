<?php

declare(strict_types=1);

namespace App\Payment\Service;

use App\Payment\Entity\Invoice;
use App\Payment\Repository\InvoiceRepository;
use App\Subscription\Entity\Subscription;

class InvoiceManager
{
    public function __construct(private InvoiceRepository $invoiceRepository)
    {
    }

    public function createFromSubscription(
        Subscription $subscription,
        \DateTimeInterface|null $dateTime = null
    ): Invoice {
        if (null === $dateTime) {
            $dateTime = new \DateTime();
        }

        $invoice = new Invoice();
        $invoice->setSubscription($subscription);
        $invoice->setCurrency($subscription->getVendorPlan()->getCurrency());
        $invoice->setTotalAmount($subscription->getPrice());
        $invoice->setDueDate($dateTime);

        $this->invoiceRepository->save($invoice);

        return $invoice;
    }

    public function markAsPaid(Invoice $invoice, \DateTimeInterface $paidAt): void
    {
        $invoice->setPaidAt($paidAt);

        $this->invoiceRepository->save($invoice);
    }

    public function save(Invoice $invoice): void
    {
        $this->invoiceRepository->save($invoice);
    }
}
