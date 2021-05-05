<?php

namespace App\Payment\MessageHandler;

use App\Payment\Message\PagarmeTransactionResponseReceivedEvent;
use App\Payment\Service\PaymentProcessor\Pagarme\PagarmeResponseProcessor;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PagarmeTransactionResponseHandler implements MessageHandlerInterface
{
    private PagarmeResponseProcessor $pagarmeResponseProcessor;

    private LoggerInterface $logger;

    public function __construct(
        PagarmeResponseProcessor $pagarmeResponseProcessor,
        LoggerInterface $logger
    ) {
        $this->pagarmeResponseProcessor = $pagarmeResponseProcessor;
        $this->logger = $logger;
    }

    public function __invoke(PagarmeTransactionResponseReceivedEvent $event)
    {
        $this->logger->info(
            'payment.transaction.response',
            (array) $event->getResponse()
        );

        $this->pagarmeResponseProcessor->process($event->getPaymentId(), $event->getResponse());
    }
}
