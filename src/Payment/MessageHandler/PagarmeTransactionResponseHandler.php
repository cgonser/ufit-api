<?php

declare(strict_types=1);

namespace App\Payment\MessageHandler;

use App\Payment\Message\PagarmeTransactionResponseReceivedEvent;
use App\Payment\Service\PaymentProcessor\Pagarme\PagarmeTransactionResponseProcessor;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PagarmeTransactionResponseHandler implements MessageHandlerInterface
{
    public function __construct(
        private PagarmeTransactionResponseProcessor $pagarmeTransactionResponseProcessor,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(PagarmeTransactionResponseReceivedEvent $pagarmeTransactionResponseReceivedEvent)
    {
        $this->logger->info(
            'payment.transaction.response',
            (array)$pagarmeTransactionResponseReceivedEvent->getResponse()
        );

        $this->pagarmeTransactionResponseProcessor->process(
            $pagarmeTransactionResponseReceivedEvent->getResponse(),
            $pagarmeTransactionResponseReceivedEvent->getPaymentId(),
            $pagarmeTransactionResponseReceivedEvent->getSubscriptionId()
        );
    }
}
