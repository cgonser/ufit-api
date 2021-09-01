<?php

declare(strict_types=1);

namespace App\Payment\Message;

use stdClass;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class PagarmeTransactionResponseReceivedEvent
{
    /**
     * @var string
     */
    public const NAME = 'payment.pagarme.transaction.response';

    private ?UuidInterface $subscriptionId = null;

    private ?UuidInterface $paymentId = null;

    public function __construct(private stdClass $response, ?string $subscriptionId = null, ?string $paymentId = null)
    {
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

    public function getResponse(): stdClass
    {
        return $this->response;
    }
}
