<?php

declare(strict_types=1);

namespace App\Payment\Message;

use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

class InvoicePaidEvent
{
    /**
     * @var string
     */
    public const NAME = 'invoice.paid';

    protected ?UuidInterface $invoiceId = null;

    public function __construct(UuidInterface $invoiceId, protected DateTimeInterface $dateTime)
    {
        $this->invoiceId = $invoiceId;
    }

    public function getInvoiceId(): ?UuidInterface
    {
        return $this->invoiceId;
    }

    public function getPaidAt(): DateTimeInterface
    {
        return $this->dateTime;
    }
}
