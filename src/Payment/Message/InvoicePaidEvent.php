<?php

declare(strict_types=1);

namespace App\Payment\Message;

use Ramsey\Uuid\UuidInterface;

class InvoicePaidEvent
{
    public const NAME = 'invoice.paid';

    protected ?UuidInterface $invoiceId = null;

    protected \DateTimeInterface $paidAt;

    public function __construct(UuidInterface $invoiceId, \DateTimeInterface $paidAt)
    {
        $this->invoiceId = $invoiceId;
        $this->paidAt = $paidAt;
    }

    public function getInvoiceId(): ?UuidInterface
    {
        return $this->invoiceId;
    }

    public function getPaidAt(): \DateTimeInterface
    {
        return $this->paidAt;
    }
}
