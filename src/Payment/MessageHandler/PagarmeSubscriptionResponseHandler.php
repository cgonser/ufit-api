<?php

declare(strict_types=1);

namespace App\Payment\MessageHandler;

use App\Payment\Message\PagarmeSubscriptionResponseReceivedEvent;
use App\Payment\Service\PaymentProcessor\Pagarme\PagarmeSubscriptionResponseProcessor;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PagarmeSubscriptionResponseHandler implements MessageHandlerInterface
{
    public function __construct(
        private PagarmeSubscriptionResponseProcessor $pagarmeSubscriptionResponseProcessor,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(PagarmeSubscriptionResponseReceivedEvent $pagarmeSubscriptionResponseReceivedEvent)
    {
        $this->logger->info(
            'payment.subscription.response',
            (array)$pagarmeSubscriptionResponseReceivedEvent->getResponse()
        );

        $this->pagarmeSubscriptionResponseProcessor->process(
            $pagarmeSubscriptionResponseReceivedEvent->getResponse(),
            $pagarmeSubscriptionResponseReceivedEvent->getSubscriptionId(),
            $pagarmeSubscriptionResponseReceivedEvent->getPaymentId()
        );
    }
}
