<?php

namespace App\Payment\Message;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class PagarmeTransactionResponseReceivedEvent
{
    public const NAME = 'payment.pagarme.transaction.response';

    private \stdClass $response;

    private ?UuidInterface $paymentId = null;

    public function __construct(\stdClass $response, ?string $paymentId = null)
    {
        $this->paymentId = Uuid::fromString($paymentId);
        $this->response = $response;
    }

    public function getPaymentId(): ?UuidInterface
    {
        return $this->paymentId;
    }

    public function getResponse(): \stdClass
    {
        return $this->response;
    }
}
