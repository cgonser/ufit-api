<?php

namespace App\Payment\Service\PaymentProcessor;

use App\Payment\Dto\PagarmeTransactionInputDto;
use App\Payment\Entity\Payment;
use App\Payment\Message\PagarmeTransactionResponseReceivedEvent;
use PagarMe\Client;
use Symfony\Component\Messenger\MessageBusInterface;

abstract class PagarmeProcessor
{
    private Client $pagarmeClient;

    private MessageBusInterface $messageBus;

    public function __construct(Client $pagarmeClient, MessageBusInterface $messageBus)
    {
        $this->pagarmeClient = $pagarmeClient;
        $this->messageBus = $messageBus;
    }

    public function process(Payment $payment)
    {
        $this->validate($payment);

        $transactionInput = $this->prepareTransactionInput($payment);

        $transactionData = $this->prepareTransactionData($transactionInput);

        $response = $this->pagarmeClient->transactions()->create($transactionData);

        $this->messageBus->dispatch(
            new PagarmeTransactionResponseReceivedEvent($payment->getId(), $response)
        );
    }

    abstract protected function validate(Payment $payment);

    abstract protected function prepareTransactionData(PagarmeTransactionInputDto $transactionInput): array;
}