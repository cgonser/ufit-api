<?php

namespace App\Payment\Message;

use Ramsey\Uuid\UuidInterface;

class PagarmeTransactionResponseReceivedEvent
{
    public const NAME = 'payment.pagarme.transaction.response';

    private UuidInterface $paymentId;

    private \stdClass $response;

    public function __construct(UuidInterface $paymentId, \stdClass $response)
    {
        $this->paymentId = $paymentId;
        $this->response = $response;
    }

    public function getPaymentId(): UuidInterface
    {
        return $this->paymentId;
    }

    public function getResponse(): \stdClass
    {
        return $this->response;
    }
}
