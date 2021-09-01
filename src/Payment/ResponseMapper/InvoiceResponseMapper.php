<?php

declare(strict_types=1);

namespace App\Payment\ResponseMapper;

use App\Localization\ResponseMapper\CurrencyResponseMapper;
use App\Payment\Dto\InvoiceDto;
use App\Payment\Entity\Invoice;
use App\Subscription\ResponseMapper\SubscriptionResponseMapper;

class InvoiceResponseMapper
{
    public function __construct(
        private SubscriptionResponseMapper $subscriptionResponseMapper,
        private CurrencyResponseMapper $currencyResponseMapper
    ) {
    }

    public function map(Invoice $invoice, bool $mapSubscription = false, bool $mapCurrency = true): InvoiceDto
    {
        $invoiceDto = new InvoiceDto();
        $invoiceDto->id = $invoice->getId()->toString();
        $invoiceDto->subscriptionId = $invoice->getSubscriptionId()->toString();
        $invoiceDto->currencyId = $invoice->getCurrencyId()->toString();
        $invoiceDto->totalAmount = $invoice->getTotalAmount()?->toString();
        $invoiceDto->dueDate = $invoice->getDueDate()?->format('Y-m-d');
        $invoiceDto->paidAt = $invoice->getPaidAt()?->format(\DateTimeInterface::ATOM);
        $invoiceDto->overdueNotificationSentAt = $invoice->getOverdueNotificationSentAt()?->format(
            \DateTimeInterface::ATOM
        );
        $invoiceDto->createdAt = $invoice->getCreatedAt()->format(\DateTimeInterface::ATOM);
        $invoiceDto->updatedAt = $invoice->getUpdatedAt()->format(\DateTimeInterface::ATOM);

        if ($mapSubscription) {
            $invoiceDto->subscription = $this->subscriptionResponseMapper->map($invoice->getSubscription());
        }

        if ($mapCurrency) {
            $invoiceDto->currency = $this->currencyResponseMapper->map($invoice->getCurrency());
        }

        return $invoiceDto;
    }

    /**
     * @return InvoiceDto[]
     */
    public function mapMultiple(array $invoices, bool $mapSubscription = true, bool $mapCurrency = true): array
    {
        $invoiceDtos = [];

        foreach ($invoices as $invoice) {
            $invoiceDtos[] = $this->map($invoice, $mapSubscription, $mapCurrency);
        }

        return $invoiceDtos;
    }
}
