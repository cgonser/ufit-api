<?php

namespace App\Payment\Service\PaymentProcessor\Pagarme;

use App\Payment\Entity\Payment;
use App\Subscription\Entity\Subscription;
use App\Subscription\Exception\SubscriptionNotFoundException;
use App\Subscription\Provider\SubscriptionProvider;
use App\Subscription\Service\SubscriptionManager;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\String\UnicodeString;

class PagarmeSubscriptionResponseProcessor
{
    private SubscriptionProvider $subscriptionProvider;

    private SubscriptionManager $subscriptionManager;

    private PagarmeTransactionResponseProcessor $pagarmeTransactionResponseProcessor;

    private MessageBusInterface $messageBus;

    public function __construct(
        SubscriptionProvider $subscriptionProvider,
        SubscriptionManager $subscriptionManager,
        PagarmeTransactionResponseProcessor $pagarmeTransactionResponseProcessor,
        MessageBusInterface $messageBus
    ) {
        $this->subscriptionProvider = $subscriptionProvider;
        $this->subscriptionManager = $subscriptionManager;
        $this->pagarmeTransactionResponseProcessor = $pagarmeTransactionResponseProcessor;
        $this->messageBus = $messageBus;
    }

    public function process(
        \stdClass $response,
        ?UuidInterface $subscriptionId = null,
        ?UuidInterface $paymentId = null
    ) {
        if (null !== $subscriptionId) {
            $subscription = $this->subscriptionProvider->get($subscriptionId);

            if (null === $subscription->getExternalReference()) {
                $this->subscriptionManager->defineExternalRefence($subscription, (string)$response->id);
            } elseif ($response->id !== $subscription->getExternalReference()) {
                throw new SubscriptionNotFoundException();
            }
        } else {
            $subscription = $this->subscriptionProvider->getByExternalReference((string)$response->id);
        }

        $status = new UnicodeString($response->status);
        $methodName = 'process'.ucfirst($status->camel());

        $this->pagarmeTransactionResponseProcessor->process(
            json_decode(json_encode($response->current_transaction)),
            $paymentId,
            $subscription->getId(),
        );

        if (method_exists($this, $methodName)) {
            $this->$methodName($subscription, $response);
        }
    }

    private function processTrialing(Subscription $subscription, \stdClass $response)
    {
        // not implemented
    }

    private function processPaid(Subscription $subscription, \stdClass $response)
    {
        if (!$subscription->isApproved()) {
            $this->subscriptionManager->approve($subscription);
        }
    }

    private function processPendingPayment(Subscription $subscription, \stdClass $response)
    {
        // todo: handle due payments
    }

    private function processUnpaid(Subscription $subscription, \stdClass $response)
    {
        // todo: handle due payments
    }

    private function processCanceled(Subscription $subscription, \stdClass $response)
    {
        $this->subscriptionManager->customerCancellation($subscription);
        // todo: what else to trigger?
    }

    private function processEnded(Subscription $subscription, \stdClass $response)
    {
        $this->subscriptionManager->expire($subscription);
        // todo: what else to trigger?
    }
}
