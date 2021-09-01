<?php

declare(strict_types=1);

namespace App\Payment\Message;

use stdClass;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class PagarmeSubscriptionResponseReceivedEvent
{
    /**
     * @var string
     */
    public const NAME = 'payment.pagarme.subscription.response';

    private ?UuidInterface $paymentId = null;

    private ?UuidInterface $subscriptionId = null;

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
