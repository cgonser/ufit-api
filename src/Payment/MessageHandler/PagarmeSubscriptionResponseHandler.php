<?php

namespace App\Payment\MessageHandler;

use App\Payment\Message\PagarmeSubscriptionResponseReceivedEvent;
use App\Payment\Service\PaymentProcessor\Pagarme\PagarmeSubscriptionResponseProcessor;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PagarmeSubscriptionResponseHandler implements MessageHandlerInterface
{
    private PagarmeSubscriptionResponseProcessor $pagarmeResponseProcessor;

    private LoggerInterface $logger;

    public function __construct(
        PagarmeSubscriptionResponseProcessor $pagarmeResponseProcessor,
        LoggerInterface $logger
    ) {
        $this->pagarmeResponseProcessor = $pagarmeResponseProcessor;
        $this->logger = $logger;
    }

    public function __invoke(PagarmeSubscriptionResponseReceivedEvent $event)
    {
        $this->logger->info(
            'payment.subscription.response',
            (array) $event->getResponse()
        );

        $this->pagarmeResponseProcessor->process($event->getResponse(), $event->getSubscriptionId(), $event->getPaymentId());
    }
}
