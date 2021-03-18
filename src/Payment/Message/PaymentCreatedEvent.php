<?php

namespace App\Payment\Message;

use Ramsey\Uuid\UuidInterface;

class PaymentCreatedEvent
{
    public const NAME = 'payment.created';

    protected ?UuidInterface $paymentId = null;

    public function __construct(UuidInterface $paymentId)
    {
        $this->paymentId = $paymentId;
    }

    public function getPaymentId(): ?UuidInterface
    {
        return $this->paymentId;
    }
}
