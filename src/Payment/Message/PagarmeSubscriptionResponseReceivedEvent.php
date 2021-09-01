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

    public function __construct(
        private stdClass $response,
        private ?UuidInterface $subscriptionId = null,
        private ?UuidInterface $paymentId = null
    ) {
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
