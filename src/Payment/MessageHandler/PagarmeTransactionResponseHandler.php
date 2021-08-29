<?php

declare(strict_types=1);

namespace App\Payment\MessageHandler;

use App\Payment\Message\PagarmeTransactionResponseReceivedEvent;
use App\Payment\Service\PaymentProcessor\Pagarme\PagarmeTransactionResponseProcessor;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PagarmeTransactionResponseHandler implements MessageHandlerInterface
{
    private PagarmeTransactionResponseProcessor $pagarmeResponseProcessor;

    private LoggerInterface $logger;

    public function __construct(
        PagarmeTransactionResponseProcessor $pagarmeResponseProcessor,
        LoggerInterface $logger
    ) {
        $this->pagarmeResponseProcessor = $pagarmeResponseProcessor;
        $this->logger = $logger;
    }

    public function __invoke(PagarmeTransactionResponseReceivedEvent $event)
    {
        $this->logger->info('payment.transaction.response', (array) $event->getResponse());

        $this->pagarmeResponseProcessor->process(
            $event->getResponse(),
            $event->getPaymentId(),
            $event->getSubscriptionId()
        );
    }
}
