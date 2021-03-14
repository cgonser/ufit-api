<?php

namespace App\Payment\Message;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class InvoicePaidEvent
{
    public const NAME = 'invoice.paid';

    protected ?UuidInterface $invoiceId = null;

    public function __construct(Uuid $invoiceId)
    {
        $this->invoiceId = $invoiceId;
    }

    public function getInvoiceId(): ?UuidInterface
    {
        return $this->invoiceId;
    }
}
