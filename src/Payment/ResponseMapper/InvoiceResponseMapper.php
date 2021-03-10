<?php

namespace App\Payment\ResponseMapper;

use App\Core\ResponseMapper\CurrencyResponseMapper;
use App\Payment\Dto\InvoiceDto;
use App\Payment\Entity\Invoice;
use App\Subscription\ResponseMapper\SubscriptionResponseMapper;

class InvoiceResponseMapper
{
    private SubscriptionResponseMapper $subscriptionResponseMapper;

    private CurrencyResponseMapper $currencyResponseMapper;

    public function __construct(
        SubscriptionResponseMapper $subscriptionResponseMapper,
        CurrencyResponseMapper $currencyResponseMapper
    ) {
        $this->subscriptionResponseMapper = $subscriptionResponseMapper;
        $this->currencyResponseMapper = $currencyResponseMapper;
    }

    public function map(Invoice $invoice, bool $mapSubscription = false, bool $mapCurrency = true): InvoiceDto
    {
        $invoiceDto = new InvoiceDto();
        $invoiceDto->id = $invoice->getId()->toString();
        $invoiceDto->subscriptionId = $invoice->getSubscriptionId()->toString();
        $invoiceDto->currencyId = $invoice->getCurrencyId()->toString();
        $invoiceDto->totalAmount = $invoice->getTotalAmount();
        $invoiceDto->dueDate = $invoice->getDueDate()->format('Y-m-d');
        $invoiceDto->paidAt = null !== $invoice->getPaidAt()
            ? $invoice->getPaidAt()->format(\DateTimeInterface::ISO8601)
            : null;
        $invoiceDto->overdueNotificationSentAt = null !== $invoice->getOverdueNotificationSentAt()
            ? $invoice->getOverdueNotificationSentAt()->format(\DateTimeInterface::ISO8601)
            : null;
        $invoiceDto->createdAt = $invoice->getCreatedAt()->format(\DateTimeInterface::ISO8601);
        $invoiceDto->updatedAt = $invoice->getUpdatedAt()->format(\DateTimeInterface::ISO8601);

        if ($mapSubscription) {
            $invoiceDto->subscription = $this->subscriptionResponseMapper->map($invoice->getSubscription());
        }

        if ($mapCurrency) {
            $invoiceDto->currency = $this->currencyResponseMapper->map($invoice->getCurrency());
        }

        return $invoiceDto;
    }

    public function mapMultiple(array $invoices, bool $mapSubscription = true, bool $mapCurrency = true): array
    {
        $invoiceDtos = [];

        foreach ($invoices as $invoice) {
            $invoiceDtos[] = $this->map($invoice, $mapSubscription, $mapCurrency);
        }

        return $invoiceDtos;
    }
}
