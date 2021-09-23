<?php

declare(strict_types=1);

namespace App\Payment\Message;

use Ramsey\Uuid\UuidInterface;

class PaymentCreatedEvent
{
    /**
     * @var string
     */
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
