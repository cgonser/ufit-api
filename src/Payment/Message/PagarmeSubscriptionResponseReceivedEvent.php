<?php

namespace App\Payment\Message;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class PagarmeSubscriptionResponseReceivedEvent
{
    public const NAME = 'payment.pagarme.subscription.response';

    private ?UuidInterface $paymentId = null;

    private ?UuidInterface $subscriptionId = null;

    private \stdClass $response;

    public function __construct(\stdClass $response, ?string $subscriptionId = null, ?string $paymentId = null)
    {
        $this->response = $response;
        $this->subscriptionId = null !== $subscriptionId ? Uuid::fromString($subscriptionId) : null;
        $this->paymentId = null !== $paymentId ? Uuid::fromString($paymentId) : null;
    }

    public function getSubscriptionId(): ?UuidInterface
    {
        return $this->subscriptionId;
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
